<?php
namespace qf\utils\exception;

use Exception;
use Throwable;

class ApiException extends Exception
{
    protected $data = [];

    public function __construct($message = "", $data = [], $code = 500, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}