<?php
namespace qf\utils\exception\handler;

use Throwable;

class ExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var
     */
    protected $code;

    /**
     * @var
     */
    protected $msg;

    /**
     * @var array
     */
    protected $data = [];

    public function handle(Throwable $exception)
    {
        $this->code = $exception->getCode();
        $this->msg = $exception->getMessage();
        $this->data = method_exists($exception, 'getData') ? $exception->getData() : $this->data;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getData()
    {
        return $this->data;
    }
}