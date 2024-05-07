<?php
namespace qf\utils\exception\handler;

use Throwable;

class ApiExceptionHandler extends ExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(Throwable $exception)
    {
        $this->code = $exception->getCode();
        $this->msg = $exception->getMessage();
        $this->data = method_exists($exception, 'getData') ? $exception->getData() : $this->data;
        return $this;
    }
}