<?php

namespace App\Traits\Responses;

use Illuminate\Http\Response;

trait ApiResponse {
    public function responseOk($data = null) {
        return response()->json($data ? [
            'data' => $data,
        ] : null, Response::HTTP_OK);
    }

    public function responseCreated($data = null) {
        return response()->json($data ? [
            'data' => $data,
        ] : null, Response::HTTP_CREATED);
    }

    public function responseDeleted() {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
