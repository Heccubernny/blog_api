<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    protected function successResponse($data = null, $message = 'Success', $code = Response::HTTP_OK)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse($message = 'Error', $code = Response::HTTP_BAD_REQUEST, $errors = null)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
