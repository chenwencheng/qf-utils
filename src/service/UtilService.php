<?php
namespace qf\utils\service;

use think\Service;

class UtilService extends Service
{
    public function register(): void
    {
        $this->bootConfig();

        if ($this->app->config->get('utils.replace_exception_handler')) {

            $this->app->bind(\think\exception\Handle::class, function () {

                return invoke(\qf\utils\provider\ExceptionHandle::class);
            });
        }
    }

    public function boot(): void
    {

    }

    public function bootConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/utils.php', 'utils');
    }

    /**
     * @param string $path
     * @param string $key
     * @return void
     */
    protected function mergeConfigFrom(string $path, string $key)
    {
        $config = $this->app->config->get($key, []);
        $this->mergeConfig(require_once $path, $key);
    }

    protected function mergeConfig(array $config, string $key)
    {
        $original = $this->app->config->get($key, []);
        $this->app->config->set(array_merge($config, $original), $key);
    }
}