<?php
namespace qf\utils\provider;

use qf\utils\exception\handler\ExceptionHandler;
use qf\utils\exception\handler\ExceptionHandlerInterface;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        $data = [];
        if ($this->app->isDebug()) {
            $data['debug'] = $this->formatExceptionLog($e);
        }

        $exceptionClass = get_class($e);
        // 查找异常处理的 handler
        $exceptionHandler = config('utils.exception_handler.' . $exceptionClass, 'qf\\utils\\exception\\handler\\' . class_basename($exceptionClass) . 'Handler');
        if (!class_exists($exceptionHandler) || !method_exists($exceptionHandler, 'handle')) {

            $handler = new ExceptionHandler();
        } else {
            /**
             * @var ExceptionHandlerInterface $handler
             */
            $handler = (new $exceptionHandler)->handle($e);
        }
        $code = $handler->getCode() ?: 500;
        $message = $handler->getMsg() ?: 'error';
        $data = method_exists($e, 'getData') ? array_merge($data, $e->getData()) : $data;
        $this->logException($e);
        return api_error($message, $code, $data);

        // 其他错误交给系统处理
        // return parent::render($request, $e);
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    protected function logException(Throwable $exception)
    {
        $ignoreReport =  $this->app->config->get('utils.ignore_report', []);
        $this->ignoreReport =  array_merge($this->ignoreReport, $ignoreReport);

        if ($this->isIgnoreReport($exception)) {
            return;
        }
        $logData = $this->formatExceptionLog($exception);
        $message = json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->app->log->error($message);
    }

    /**
     * @param Throwable $exception
     * @return array
     */
    protected function formatExceptionLog(Throwable $exception)
    {
        return [
            'exception' => get_class($exception),
            'code' => $this->getCode($exception),
            'message' => $this->getMessage($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'source' => $this->getSourceCode($exception),
        ];
    }
}
