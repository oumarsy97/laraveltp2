<?php
namespace App\Traits;

trait ApiResponser
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => $code,
            'data' => $data,
            'message' => $message
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'status' => $code,
            'data' => null,
            'message' => $message
        ], $code);
    }

     static function sendResponse($data, $message = null, $code =200 , $status)
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $code);
    }
}
