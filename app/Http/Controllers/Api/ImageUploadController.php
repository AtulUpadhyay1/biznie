<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function imageUpload(Request $request)
    {
        $this->validate($request,[
            'image'     => 'required|file|mimes:jpeg,jpg,png,gif,webp,pdf',
            'purpose'   => 'required'
        ]);

        try {

            $id = imageUpload($request->image, $request->purpose);
            return response([
                'success'   => true,
                'id'        => $id,
                'message'   => 'Flie Upload Successfully.'
            ],200);

        } catch (\Throwable $th) {
            return response([
                'success'   => false,
                'message'   => 'Something went wrong. Please try again.',
                'error'     => $th->getMessage()
            ],500);

        }
    }
}
