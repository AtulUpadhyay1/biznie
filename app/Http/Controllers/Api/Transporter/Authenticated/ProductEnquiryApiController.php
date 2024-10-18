<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductEnquiry;
use App\Http\Controllers\Controller;
use App\Models\TransporterProductEnquiry;
use App\Http\Resources\Transporter\ProductEnquiryResource;

class ProductEnquiryApiController extends Controller
{
    public function index(Request $request)
    {
        $list = TransporterProductEnquiry::where('user_id', auth()->id())->latest()->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory')->paginate(getPaginate());
        return ProductEnquiryResource::collection($list);
    }

    public function show($id)
    {
        $data = TransporterProductEnquiry::where('user_id', auth()->id())->with('getBrand', 'getCommodityProduct', 'getCommodityProduct.getCategory')->find($id);

        return response([
            'success'   => true,
            'data'      => new ProductEnquiryResource($data)
        ],200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required'
        ]);

        $data = TransporterProductEnquiry::where('user_id', auth()->id())->find($id);
        $data->price = $request->price;
        $data->status = 'replied';
        $history = $data->history;
        $history[] = ['status' => 'Replied', 'created_at' => Carbon::now()];
        $data->history = $history;
        $data->save();

        $enquiry = ProductEnquiry::findOrFail($data->product_enquiries_id);
        if($enquiry){
            if($enquiry->status != 'Transporter Replied'){
                $enquiry->status = 'Transporter Replied';
                $history = $enquiry->history;
                $history[] = ['status' => 'Transporter Replied', 'created_at' => Carbon::now()];
                $enquiry->history = $history;
                $enquiry->save();
            }
        }

        return response([
            'success'   => true,
            'message'   => 'Product enquiry updated successfully.',
            'data'      => new ProductEnquiryResource($data)
        ],200);
    }
}
