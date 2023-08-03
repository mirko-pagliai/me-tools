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
 * `isIp()` detector.
 *
 * Checks if the specified client IP is the current client IP.
 * The client IP can be passed as string or array.
 *
 * Examples:
 * <code>
 * $this->getRequest()->is('ip', '192.168.0.100');
 * $this->getRequest()->is('ip', ['192.168.0.100', '192.168.1.100']);
 * </code>
 */
ServerRequest::addDetector('ip', fn(ServerRequest $request, $ip): bool => in_array($request->clientIp(), (array)$ip));

/**
 * `isMatchingIp()` detector.
 *
 * This works like the `isIp()` detector, but can take the use of the `*` asterisk and check the IP address with a regex.
 * For each asterisk `*` it will expect a sequence between 1 and 3 digits (`\d{1,3}`).
 * The client IP can be passed as string or array.
 *
 * Examples:
 * <code>
 * $this->getRequest()->is('matchingIp', ['10.0.*.*', '192.168.0.*']);
 * </code>
 *
 * In this case the checked regex will be:
 * <code>
 * /^(10\.0\.\d{1,3}\.\d{1,3}|192\.168\.0\.\d{1,3})$/
 * </code>
 * and will return `true` for all IP addresses starting with `10.0` or `192.168.0`.
 *
 * Pay particular attention not to overuse the `*` asterisks to avoid unexpected results.
 */
ServerRequest::addDetector('matchingIp', function (ServerRequest $request, $wildCardIp): bool {
    $wildCardIp = array_map(fn(string $value): string => str_replace(['.', '*'], ['\.', '\d{1,3}'], $value), (array)$wildCardIp);

    return preg_match(sprintf('/^(%s)$/', implode('|', $wildCardIp)), $request->clientIp()) === 1;
});

/**
 * `isLocalhost()` detector.
 *
 * Checks if the client IP is the localhost.
 */
ServerRequest::addDetector('localhost', fn(ServerRequest $request): bool => $request->is('ip', ['127.0.0.1', '::1']));

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
