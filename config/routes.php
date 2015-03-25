<?php
use Cake\Routing\Router;

Router::plugin('MeTools', function ($routes) {
    $routes->fallbacks();
});
