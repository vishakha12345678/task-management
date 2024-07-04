<?php

namespace App\Traits;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

trait ApiResponse
{
    /**
     * HTTP Status Code
     *
     * @var string
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * Get the HTTP Status Code
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set the HTTP Status Code
     *
     * @param  string $statusCode
     * @return APIController
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Create JSON Response
     *
     * @param  array $data
     * @param  array=[] $headers
     * @return Response
     */
    public function response($data, $headers = [])
    {
        return response()->json($data, 200, $headers);
    }

    /**
     * Create Success JSON Response
     *
     * @param  array $data
     * @param  array=[] $headers
     * @return Response
     */
    public function successResponse($data = [], $headers = [])
    {
        $data["error"] = false;
        $data = $data + [
            'message' => "Ok",
            'data' => []
        ];
        $data = array_merge(array_flip(array('message', 'data')), $data);

        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function failResponse($data = [], $errorCode = 200, $headers = [])
    {
        $data["error"] = true;
        $data = $data + [
            'message' => trans('messages.something_went_wrong'),
        ];
        $errorCode = $errorCode != 0 ? $errorCode : 200;
        $errorCode = ((intval($errorCode) > 599 || intval($errorCode) < 100) ? 500 : intval($errorCode));
        $this->setStatusCode($errorCode);
        $data = array_merge(array_flip(array('message')), $data);
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function NotFoundResponse($data = [], $errorCode = 200, $headers = [])
    {
        $data["error"] = false;
        $data = $data + [
            'message' => trans('messages.something_went_wrong'),
        ];
        $errorCode = $errorCode != 0 ? $errorCode : 200;
        $errorCode = ((intval($errorCode) > 599 || intval($errorCode) < 100) ? 500 : intval($errorCode));
        $this->setStatusCode($errorCode);
        $data = array_merge(array_flip(array('message')), $data);
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function forgotPassResponse($data = [], $headers = [])
    {
        $data["error"] = false;
        $data = $data + [
            'message' => "Ok",
            'token' => []
        ];
        $data = array_merge(array_flip(array('message', 'token')), $data);

        return response()->json($data, $this->getStatusCode(), $headers);
    }
}
