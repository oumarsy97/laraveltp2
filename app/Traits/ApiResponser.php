<?php
namespace App\Traits;

use App\Enums\ResponseStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
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
            'message' => $message,
            'data' => $data
        ], $code);
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse(null, $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY,ResponseStatus::ECHEC)
        );
    }
}
