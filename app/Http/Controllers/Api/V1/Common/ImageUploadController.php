<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class ImageUploadController extends Controller {
    public function storeS3(Request $request) {
        $path = Storage::disk(name: 's3')->put('images', $request->image);

        return response()->json([
            'path' => Storage::disk('s3')->url($path)
        ]);
    }
}
