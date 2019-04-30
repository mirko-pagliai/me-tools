<?php
/**
 * This file is part of cakephp-assets.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/cakephp-assets
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       2.17.3
 */
namespace App;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;
use MeTools\Plugin as MeTools;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
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
