<?php

namespace App\Http\Controllers\Api\Transporter\Authenticated;

use Illuminate\Http\Request;
use App\Models\CommodityProduct;
use App\Models\TransporterDetail;
use App\Http\Controllers\Controller;

class ProductApiController extends Controller
{
    public function index()
    {
        $list = CommodityProduct::active()->latest()->with('getCategory')->select(['id', 'name', 'thumbnail', 'category_id'])->get();
        foreach ($list as $data) {
            $data->thumbnail = imageUrl($data->thumbnail);
            $data->category_name = $data->getCategory->name;
            $data->is_selected = auth()->user()->getTransporterDetail->commodity_product && in_array($data->id, auth()->user()->getTransporterDetail->commodity_product) ? true : false;
        }

        return response([
            'success'   => true,
            'list'      => $list
        ],200);
    }

    public function assignProduct(Request $request)
    {
        $request->validate([
            'vehicle' =>'nullable|array'
        ]);

        $transporter = TransporterDetail::where('user_id', auth()->id())->first();
        $transporter->commodity_product = $request->commodity_product;
        $transporter->save();

        return response([
            'success'   => true,
            'message'   => 'Vehicle assigned successfully.',
        ],200);
    }
}
