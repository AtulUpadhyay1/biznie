<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryApiController extends Controller
{
    public function Category()
    {
        return response([
            'success'           => true,
            'category'          => CategoryResource::collection(ProductCategory::active()->with('getSubCategory')->get())
        ], 200);
    }
}
