<?php
/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

/**
 * Adds `is('action')` detector.
 *
 * It checks if the specified action is the current action.
 * The action name can be passed as string or array.
 *
 * Optionally, you can also check if the specified controller is the
 * current controller.
 *
 * The controller name can be passed as string or array.
 *
 * Example:
 * <code>
 * $this->request->isAction('delete');
 * </code>
 * returns `true` if the current action is `delete`, otherwise `false`.
 *
 * Example:
 * <code>
 * $this->request->isAction(['edit', 'delete'], 'Pages');
 * </code>
 * returns `true` if the current action is `edit` or `delete` and if the
 * current controller is `Pages`, otherwise `false`.
 */
ServerRequest::addDetector('action', function (ServerRequest $request, $action, $controller = null) {
    $action = in_array($request->getParam('action'), (array)$action);

    //Checks only action
    if (empty($controller)) {
        return $action;
    }

    //Checks action and controller
    return $action && $request->is('controller', $controller);
});

/**
 * Adds `is('controller')` detector.
 *
 * It checks if the specified controller is the current controller.
 * The controller name can be passed as string or array.
 *
 * Example:
 * <code>
 * $this->request->isController('Pages');
 * </code>
 * returns `true` if the current controller is `Pages`, otherwise `false`.
 */
ServerRequest::addDetector('controller', function (ServerRequest $request, $controller) {
    return in_array($request->getParam('controller'), (array)$controller);
});

/**
 * Adds `is('localhost')` detector.
 *
 * It checks if the host is the localhost.
 */
ServerRequest::addDetector('localhost', function (ServerRequest $request) {
    return in_array($request->clientIp(), ['127.0.0.1', '::1']);
});

/**
 * Adds `is('prefix')` detector.
 *
 * It checks if the specified prefix is the current prefix.
 * The prefix name can be passed as string or array.
 *
 * Example:
 * <code>
 * $this->request->isPrefix(['admin', 'manager']);
 * </code>
 */
ServerRequest::addDetector('prefix', function (ServerRequest $request, $prefix) {
    return in_array($request->getParam('prefix'), (array)$prefix);
});

/**
 * Adds `is('url')` detector.
 *
 * It checks if the specified url is the current url.
 *
 * Example:
 * <code>
 * $this->request->isUrl(['_name' => 'posts']);
 * </code>
 *
 * The first argument is the url to be verified as an array of parameters or a
 *  string. The second argument allows you to not remove the query string from
 *  the current url.
 */
ServerRequest::addDetector('url', function (ServerRequest $request, $url, $removeQueryString = true) {
    $current = rtrim($request->getEnv('REQUEST_URI'), '/');

    if ($removeQueryString) {
        $current = explode('?', $current, 2)[0];
    }

    return rtrim(Router::url($url), '/') === $current;
});
