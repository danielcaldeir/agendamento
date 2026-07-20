<?php

namespace App\Http\Controllers\Responses;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceResponse extends Controller implements Responsable
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $errors;

    public function __construct(
        bool $success = true,
        string $message = '',
        $data=null,
        $errors=null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->data    = $data;
        $this->errors  = $errors;
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
            'errors'  => $this->errors
        ]);
    }

    public function successResponse(
        $message="Request Success",
        $data=null,
        $statusCode=200
    ){
        $this->success = true;
        $this->message = $message;
        $this->data    = $data;
        $this->errors  = $statusCode;
        return new JsonResponse([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => $statusCode
        ]);
    }

    public function errorResponse(
        $message="Request Fail",
        $data=null,
        $statusCode=400
    ){
        $this->success = false;
        $this->message = $message;
        $this->data    = $data;
        $this->errors  = $statusCode;
        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'errors' => $statusCode
        ], $statusCode);
    }

    public function dataResponse($data=null){
        $this->success = true;
        $this->message = 'Request Success';
        $this->data    = $data;
        $this->errors  = '200';
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Request Success',
            'data' => $data,
            'errors' => '200'
        ]);
    }

    public function arrayData($data=null){
        // $this->success = $data->success;
        // $this->message = $data->message;
        // $this->data    = $data;
        // $this->errors  = $data->errors;
        return new JsonResponse([$data]);
    }

    public function getSuccess(){
        return $this->success;
    }

    public function getMessage(){
        return $this->message;
    }

    public function getData(){
        return $this->data;
    }

    public function getErrors(){
        return $this->errors;
    }
}
