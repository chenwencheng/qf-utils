<?php

use qf\utils\exception\ApiException;

if (!function_exists('throw_api')) {

    /**
     * @param $message
     * @param $data
     * @return mixed
     * @throws ApiException
     */
    function throw_api($message = 'api error', $data = [])
    {
        throw new ApiException($message, $data);
    }
}

if (!function_exists('throw_api_if')) {

    /**
     * @param $condition
     * @param ...$parameters
     * @return mixed
     */
    function throw_api_if($condition, ...$parameters)
    {
        return throw_if($condition, ApiException::class, ...$parameters);
    }
}

if (!function_exists('throw_if')) {

    /**
     * @param $condition
     * @param $exception
     * @param ...$parameters
     * @return mixed
     */
    function throw_if($condition, $exception = RuntimeException::class, ...$parameters)
    {
        if ($condition) {
            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }

            throw is_string($exception) ? new RuntimeException($exception) : $exception;
        }

        return $condition;
    }
}

if (!function_exists('api_success')) {

    /**
     * @param $data
     * @param $message
     * @param $code
     * @param $header
     * @param $options
     * @return \think\response\Json
     */
    function api_success($data = [], $message = 'success', $code = 200, $header = [], $options = [])
    {
        return api_response($data, $message, $code, $header, $options);
    }
}

if (!function_exists('api_error')) {

    /**
     * @param $message
     * @param $code
     * @param $data
     * @param $header
     * @param $options
     * @return \think\response\Json
     */
    function api_error($message = 'error', $code = 500, $data = [], $header = [], $options = [])
    {
        return api_response($data, $message, $code, $header, $options);
    }
}

if (!function_exists('api_response')) {

    /**
     * @param $data
     * @param $message
     * @param $code
     * @param $header
     * @param $options
     * @return \think\response\Json
     */
    function api_response($data = [], $message = '', $code = 200, $header = [], $options = [])
    {
        return json([
            'code' => $code,
            'msg' => $message,
            'data' => $data,
        ], $code, $header, $options);
    }
}