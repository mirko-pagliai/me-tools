<?php
declare(strict_types=1);

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
 * `isAction()` detector.
 *
 * Checks if the specified action is the current action.
 * The action name can be passed as string or array.
 *
 * Optionally, you can also check if the specified controller is the current controller.
 * The controller name can be passed as string or array.
 *
 * Example:
 * <code>
 * $this->getRequest()->isAction('delete');
 * </code>
 * returns `true` if the current action is `delete`, otherwise `false`.
 *
 * Example:
 * <code>
 * $this->getRequest()->isAction(['edit', 'delete'], 'Pages');
 * </code>
 * returns `true` if the current action is `edit` or `delete` and if the current controller is `Pages`, otherwise `false`.
 */
ServerRequest::addDetector('action', function (ServerRequest $request, $action, $controller = null): bool {
    $action = in_array($request->getParam('action'), (array)$action);

    return $controller ? $action && $request->is('controller', $controller) : $action;
});

/**
 * Other "action detectors": `is('add')`, `is('edit')`, `is('view')`, `is('index')`, `is('delete')`.
 *
 * See `isAction()` detector description.
 *
 * Example:
 * <code>
 * $this->getRequest()->isDelete();
 * </code>
 * returns `true` if the current action is `delete`, otherwise `false`.
 *
 * Example:
 * <code>
 * $this->getRequest()->isDelete('Pages');
 * </code>
 * returns `true` if the current action is `delete` and if the current controller is `Pages`, otherwise `false`.
 */
ServerRequest::addDetector('index', fn(ServerRequest $request, $controller = null): bool => $request->is('action', 'index', $controller));
ServerRequest::addDetector('add', fn(ServerRequest $request, $controller = null): bool => $request->is('action', 'add', $controller));
ServerRequest::addDetector('edit', fn(ServerRequest $request, $controller = null): bool => $request->is('action', 'edit', $controller));
ServerRequest::addDetector('delete', fn(ServerRequest $request, $controller = null): bool => $request->is('action', 'delete', $controller));
ServerRequest::addDetector('view', fn(ServerRequest $request, $controller = null): bool => $request->is('action', 'view', $controller));

/**
 * Adds `isController()` detector.
 *
 * Checks if the specified controller is the current controller.
 * The controller name can be passed as string or array.
 *
 * Example:
 * <code>
 * $this->getRequest()->isController('Pages');
 * </code>
 * returns `true` if the current controller is `Pages`, otherwise `false`.
 */
ServerRequest::addDetector('controller', fn(ServerRequest $request, $controller): bool => in_array($request->getParam('controller'), (array)$controller));

/**
 * `isLocalhost()` detector.
 *
 * Checks if the host is the localhost.
 */
ServerRequest::addDetector('localhost', fn(ServerRequest $request): bool => in_array($request->clientIp(), ['127.0.0.1', '::1']));

/**
 * `isPrefix()` detector.
 *
 * Checks if the specified prefix is the current prefix.
 * The prefix name can be passed as string or array.
 *
 * Example:
 * <code>
 * $this->getRequest()->isPrefix(['admin', 'manager']);
 * </code>
 */
ServerRequest::addDetector('prefix', fn(ServerRequest $request, $prefix): bool => in_array($request->getParam('prefix'), (array)$prefix));

/**
 * `isUrl()` detector.
 *
 * Checks if the specified url is the current url.
 *
 * The first argument is the url to be verified as an array of parameters or a
 *  string. The second argument allows you to not remove the query string from
 *  the current url.
 *
 * Example:
 * <code>
 * $this->getRequest()->isUrl(['_name' => 'posts']);
 * </code>
 */
ServerRequest::addDetector('url', function (ServerRequest $request, $url, bool $removeQueryString = true): bool {
    $current = rtrim($request->getEnv('REQUEST_URI') ?: '', '/');
    $current = $removeQueryString ? explode('?', $current, 2)[0] : $current;

    return rtrim(Router::url($url), '/') === $current;
});
