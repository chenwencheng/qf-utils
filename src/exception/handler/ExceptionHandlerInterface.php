<?php
namespace qf\utils\exception\handler;

use Throwable;

interface ExceptionHandlerInterface
{
    public function handle(Throwable $exception);
}