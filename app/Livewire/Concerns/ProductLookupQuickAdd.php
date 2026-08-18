<?php

namespace App\Livewire\Concerns;

use App\Models\Attribute;
use App\Models\PackagingType;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\ProductSubSubCategory;
use App\Models\ProductUnit;
use Illuminate\Support\Str;

/**
 * Shared by the commodity-product Create and Edit screens.
 *
 * Two jobs:
 *  1. The Category -> Sub Category -> Sub Sub Category cascade. This used to be
 *     driven by jQuery `change` handlers bound at script-parse time, which never
 *     attached because the elements did not exist yet, so the dependent lists
 *     stayed empty. It is now driven by Livewire `updated*` hooks, which fire no
 *     matter how the value changed.
 *  2. The "quick add" modals that let an admin create a lookup row without
 *     leaving the product form.
 *
 * Every <select> on the form lives inside `wire:ignore` so that select2's own
 * DOM survives a Livewire re-render. That means Blade cannot refresh their
 * <option>s, so option changes are pushed to the browser with a `bz-options`
 * event and applied by the bridge in biznie-admin.js.
 */
trait ProductLookupQuickAdd
{
    /* ---- quick-add modal fields ---------------------------------------- */
    public $qa_business_category_id;
    public $qa_category_name;
    public $qa_sub_category_name;
    public $qa_sub_sub_category_name;
    public $qa_unit_name;
    public $qa_unit_short_name;
    public $qa_attribute_name;
    public $qa_packaging_type_name;

    /* ---- cascade -------------------------------------------------------- */

    public function updatedCategoryId(): void
    {
        $this->sub_category_id = null;
        $this->sub_sub_category_id = null;
        $this->setSubCategoryList();
        $this->pushSubCategoryOptions();
        $this->pushSubSubCategoryOptions();
    }

    public function updatedSubCategoryId(): void
    {
        $this->sub_sub_category_id = null;
        $this->setSubSubCategoryList();
        $this->pushSubSubCategoryOptions();
    }

    public function setSubCategoryList(): void
    {
        $this->sub_category_list = $this->category_id
            ? ProductSubCategory::active()->where('product_category_id', $this->category_id)->orderBy('name')->get()
            : collect();
        $this->sub_sub_category_list = collect();
    }

    public function setSubSubCategoryList(): void
    {
        $this->sub_sub_category_list = $this->sub_category_id
            ? ProductSubSubCategory::active()->where('product_sub_category_id', $this->sub_category_id)->orderBy('name')->get()
            : collect();
    }

    /* ---- pushing options to the (wire:ignore'd) select2 controls --------- */

    /**
     * @param  iterable  $rows      rows to turn into <option>s
     * @param  mixed     $selected  scalar id, or array of ids for a multiple select
     */
    protected function pushOptions(string $target, $rows, $selected, ?string $placeholder, callable $label): void
    {
        $this->dispatch(
            'bz-options',
            target: $target,
            placeholder: $placeholder,
            options: collect($rows)->map(fn ($row) => [
                'id' => (string) $row->id,
                'text' => $label($row),
            ])->values()->all(),
            selected: is_array($selected)
                ? array_map('strval', $selected)
                : ($selected === null ? '' : (string) $selected),
        );
    }

    /** Mirrors the label Blade renders for a category option. */
    protected function categoryOptionLabel($category): string
    {
        $label = $category->name;

        if (!empty($category->attributes)) {
            $names = collect($category->attributes)
                ->map(fn ($id) => optional(getAttribute($id))->name)
                ->filter();

            if ($names->isNotEmpty()) {
                $label .= ' (' . $names->implode(', ') . ')';
            }
        }

        return $label;
    }

    protected function pushCategoryOptions(): void
    {
        $this->pushOptions(
            'category_id',
            ProductCategory::active()->orderBy('name')->get(),
            $this->category_id,
            'Select Category',
            fn ($row) => $this->categoryOptionLabel($row),
        );
    }

    protected function pushSubCategoryOptions(): void
    {
        $this->pushOptions(
            'sub_category_id',
            $this->sub_category_list,
            $this->sub_category_id,
            'Select Sub Category',
            fn ($row) => $row->name,
        );
    }

    protected function pushSubSubCategoryOptions(): void
    {
        $this->pushOptions(
            'sub_sub_category_id',
            $this->sub_sub_category_list,
            $this->sub_sub_category_id,
            'Select Sub Sub Category',
            fn ($row) => $row->name,
        );
    }

    protected function pushUnitOptions(): void
    {
        $this->pushOptions(
            'unit_id',
            ProductUnit::active()->orderBy('name')->get(),
            $this->unit_id,
            'Select Unit',
            fn ($row) => $row->short_name,
        );
    }

    protected function pushAttributeOptions(): void
    {
        $this->pushOptions(
            'attribute',
            Attribute::active()->orderBy('name')->get(),
            (array) $this->attribute,
            'Select Attribute',
            fn ($row) => $row->name,
        );
    }

    /* ---- quick add ------------------------------------------------------ */

    public function saveQuickCategory(): void
    {
        $this->validate([
            'qa_business_category_id' => 'required',
            'qa_category_name' => 'required|string|max:190',
        ], [], [
            'qa_business_category_id' => 'business category',
            'qa_category_name' => 'category name',
        ]);

        $row = new ProductCategory;
        $row->business_category_id = $this->qa_business_category_id;
        $row->name = $this->qa_category_name;
        $row->slug = $this->uniqueSlug(ProductCategory::class, $this->qa_category_name);
        $row->status = 1;
        $row->save();

        $this->category_id = $row->id;
        // A brand-new category cannot have children yet, so both dependent
        // lists collapse to empty.
        $this->setSubCategoryList();

        $this->pushCategoryOptions();
        $this->pushSubCategoryOptions();
        $this->pushSubSubCategoryOptions();
        $this->finishQuickAdd('qaCategoryModal', ['qa_category_name', 'qa_business_category_id'], 'Category added.');
    }

    public function saveQuickSubCategory(): void
    {
        $this->validate([
            'category_id' => 'required',
            'qa_sub_category_name' => 'required|string|max:190',
        ], [
            'category_id.required' => 'Choose a category first — a sub category belongs to one.',
        ], [
            'qa_sub_category_name' => 'sub category name',
        ]);

        $parent = ProductCategory::findOrFail($this->category_id);

        $row = new ProductSubCategory;
        $row->business_category_id = $parent->business_category_id;
        $row->product_category_id = $parent->id;
        $row->name = $this->qa_sub_category_name;
        $row->slug = $this->uniqueSlug(ProductSubCategory::class, $this->qa_sub_category_name);
        $row->status = 1;
        $row->save();

        $this->setSubCategoryList();
        $this->sub_category_id = $row->id;
        $this->setSubSubCategoryList();

        $this->pushSubCategoryOptions();
        $this->pushSubSubCategoryOptions();
        $this->finishQuickAdd('qaSubCategoryModal', ['qa_sub_category_name'], 'Sub category added.');
    }

    public function saveQuickSubSubCategory(): void
    {
        $this->validate([
            'sub_category_id' => 'required',
            'qa_sub_sub_category_name' => 'required|string|max:190',
        ], [
            'sub_category_id.required' => 'Choose a sub category first — a sub sub category belongs to one.',
        ], [
            'qa_sub_sub_category_name' => 'sub sub category name',
        ]);

        $parent = ProductSubCategory::findOrFail($this->sub_category_id);

        $row = new ProductSubSubCategory;
        $row->business_category_id = $parent->business_category_id;
        $row->product_category_id = $parent->product_category_id;
        $row->product_sub_category_id = $parent->id;
        $row->name = $this->qa_sub_sub_category_name;
        $row->slug = $this->uniqueSlug(ProductSubSubCategory::class, $this->qa_sub_sub_category_name);
        $row->status = 1;
        $row->save();

        $this->setSubSubCategoryList();
        $this->sub_sub_category_id = $row->id;

        $this->pushSubSubCategoryOptions();
        $this->finishQuickAdd('qaSubSubCategoryModal', ['qa_sub_sub_category_name'], 'Sub sub category added.');
    }

    public function saveQuickUnit(): void
    {
        $this->validate([
            'qa_unit_name' => 'required|string|max:190',
            'qa_unit_short_name' => 'required|string|max:50',
        ], [], [
            'qa_unit_name' => 'unit name',
            'qa_unit_short_name' => 'short name',
        ]);

        $row = new ProductUnit;
        $row->name = $this->qa_unit_name;
        $row->short_name = $this->qa_unit_short_name;
        $row->status = 1;
        $row->save();

        $this->unit_id = $row->id;

        $this->pushUnitOptions();
        $this->finishQuickAdd('qaUnitModal', ['qa_unit_name', 'qa_unit_short_name'], 'Unit added.');
    }

    public function saveQuickAttribute(): void
    {
        $this->validate([
            'qa_attribute_name' => 'required|string|max:190|unique:attributes,name',
        ], [], [
            'qa_attribute_name' => 'attribute name',
        ]);

        $row = new Attribute;
        $row->name = $this->qa_attribute_name;
        $row->status = 1;
        $row->save();

        $this->attribute = array_values(array_unique(array_merge((array) $this->attribute, [(string) $row->id])));

        $this->pushAttributeOptions();
        $this->finishQuickAdd('qaAttributeModal', ['qa_attribute_name'], 'Attribute added.');
    }

    public function saveQuickPackagingType(): void
    {
        $this->validate([
            'qa_packaging_type_name' => 'required|string|max:190|unique:packaging_types,name',
        ], [], [
            'qa_packaging_type_name' => 'packaging type name',
        ]);

        $row = new PackagingType;
        $row->name = $this->qa_packaging_type_name;
        $row->status = 1;
        $row->save();

        // Packaging types are checkboxes rendered by Blade, so the re-render
        // picks the new one up on its own — just tick it.
        $this->packaging_type = array_values(array_unique(array_merge((array) $this->packaging_type, [(string) $row->id])));

        $this->finishQuickAdd('qaPackagingTypeModal', ['qa_packaging_type_name'], 'Packaging type added.');
    }

    /* ---- helpers -------------------------------------------------------- */

    protected function finishQuickAdd(string $modalId, array $fields, string $message): void
    {
        $this->reset($fields);
        $this->resetValidation();
        $this->dispatch('bz-modal-close', id: $modalId);
        $this->dispatch('alert', type: 'success', message: $message);
    }

    /** `slug` is NOT NULL and duplicates would be confusing, so suffix collisions. */
    protected function uniqueSlug(string $model, string $name): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i = 2;

        while ($model::where('slug', $slug)->withTrashed()->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
