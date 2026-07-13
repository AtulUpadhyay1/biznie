<?php

namespace App\Livewire\Admin\SellerProductRequest;

use App\Mail\SellerProductStatusChangedMail;
use App\Models\SellerCommodityProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Show extends Component
{
    public SellerCommodityProduct $requestRecord;
    public string $reviewNote = '';

    public function mount(int $id): void
    {
        $this->authorize('seller-list');

        $request = SellerCommodityProduct::with(['getUser', 'getReviewer', 'getCategory', 'getSubCategory', 'getUnit'])
            ->findOrFail($id);
        $this->requestRecord = $request;
        $this->reviewNote = $request->review_note ?? '';
    }

    public function approve(): void
    {
        $this->authorize('seller-list');
        $record = $this->requestRecord->fresh(['getUser', 'getReviewer']);

        if ($record->request_status === 'approved') {
            $this->dispatch('alert', type: 'error', message: 'This product is already approved.');
            return;
        }

        DB::transaction(function () use ($record) {
            $record->request_status = 'approved';
            // Approved products go live; the seller controls active/inactive afterwards.
            $record->status = 'active';
            $record->reviewed_at = now();
            $record->reviewed_by = auth('admin')->id();
            $record->review_note = $this->reviewNote ?: null;
            $record->appendTimeline('approved', [
                'reviewed_by' => auth('admin')->id(),
                'note' => $this->reviewNote ?: null,
            ]);
            $record->save();
        });

        $this->requestRecord = $this->requestRecord->fresh(['getUser', 'getReviewer', 'getCategory', 'getSubCategory', 'getUnit']);

        if ($this->requestRecord->getUser?->email) {
            Mail::to($this->requestRecord->getUser->email)->send(new SellerProductStatusChangedMail($this->requestRecord));
        }

        session()->flash('success', 'Product approved and published.');
    }

    public function reject(): void
    {
        $this->authorize('seller-list');

        $this->validate([
            'reviewNote' => ['required', 'string', 'max:2000'],
        ]);

        $record = $this->requestRecord->fresh(['getUser', 'getReviewer']);
        if ($record->request_status === 'approved') {
            $this->addError('reviewNote', 'Approved products cannot be rejected.');
            return;
        }

        DB::transaction(function () use ($record) {
            $record->request_status = 'rejected';
            $record->status = 'inactive';
            $record->reviewed_at = now();
            $record->reviewed_by = auth('admin')->id();
            $record->review_note = $this->reviewNote;
            $record->appendTimeline('rejected', [
                'reviewed_by' => auth('admin')->id(),
                'note' => $this->reviewNote,
            ]);
            $record->save();
        });

        $this->requestRecord = $this->requestRecord->fresh(['getUser', 'getReviewer', 'getCategory', 'getSubCategory', 'getUnit']);

        if ($this->requestRecord->getUser?->email) {
            Mail::to($this->requestRecord->getUser->email)->send(new SellerProductStatusChangedMail($this->requestRecord));
        }

        session()->flash('success', 'Product rejected.');
    }

    public function render()
    {
        return view('admin.seller_product_request.show', [
            'page_title' => 'Review Product Request',
        ]);
    }
}
