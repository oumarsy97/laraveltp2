<?php
namespace App\Traits;

use Illuminate\Http\Response;

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

     static function sendResponse($data, $message = "ressource non trouver", $code = Response::HTTP_OK , $status)
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $code);
    }
}
