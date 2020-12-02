<?php
declare(strict_types=1);

namespace App;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;
use MeTools\Plugin as MeTools;

/**
 * Application setup class
 */
class Application extends BaseApplication
{
    /**
     * Load all the application configuration and bootstrap logic
     */
    public function bootstrap(): void
    {
        $this->addPlugin(MeTools::class);
    }

    /**
     * Define the HTTP middleware layers for an application
     * @param MiddlewareQueue $middlewareQueue The middleware queue to set in your App Class
     * @return MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue->add(new RoutingMiddleware());
    }
}
