<?php

namespace App\Livewire\Admin\SellerProductRequest;

use App\Models\SellerCommodityProduct;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = 'pending_review';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'pending_review'],
    ];

    public function mount(): void
    {
        $this->authorize('seller-list');
    }

    public function render()
    {
        $list = SellerCommodityProduct::query()
            ->whereNotNull('request_reference')
            ->with(['getUser', 'getReviewer', 'getCategory'])
            ->when($this->status, fn ($query) => $query->where('request_status', $this->status))
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($nested) use ($term) {
                    $nested->where('request_reference', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhereHas('getUser', function ($userQuery) use ($term) {
                            $userQuery->where('name', 'like', $term)
                                ->orWhere('phone', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(getPaginate());

        return view('admin.seller_product_request.index', compact('list'), [
            'page_title' => 'Product Requests',
        ]);
    }
}
