<?php

namespace App\Livewire\Admin\MarketNews;

use Livewire\Component;
use App\Models\MarketNews;
use Livewire\WithPagination;

class Index extends Component
{
    public $page_title = 'Market News List';

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    protected $queryString = [
        'search'        => ['except' => '']
    ];

    public function render()
    {
        $list = MarketNews::search($this->search)->latest()->paginate(getPaginate());
        return view('admin.market_news.index', compact('list'));
    }

    public function updateStatus($id)
    {
        try {

            $data = MarketNews::findOrFail($id);
            $data->status = $data->status ? 0 : 1;
            $data->save();

            $this->dispatch('alert',
                type : 'success',
                message : 'Market news status updated successfully.',
            );

        } catch (\Throwable $th) {
            $this->dispatch('alert',
                type : 'error',
                message : 'Something went wrong.',
            );
        }
    }

    public function delete($id)
    {
        try{
            MarketNews::destroy($id);
            $this->dispatch('alert',
                type: 'success',
                message: 'Data deleted successfully !!'
            );

        }catch (\Exception $e) {
            $this->dispatch('alert',
                type: 'error',
                message: 'Something went wrong !!'
            );
        }

    }
}
