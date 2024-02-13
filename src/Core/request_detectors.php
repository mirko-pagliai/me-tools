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
ServerRequest::addDetector('action', function (ServerRequest $Request, string|array $action, string|array|null $controller = null): bool {
    $isAction = in_array($Request->getParam('action'), (array)$action);

    return $controller ? $isAction && $Request->is('controller', $controller) : $isAction;
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
ServerRequest::addDetector('index', fn(ServerRequest $Request, string|array|null $controller = null): bool => $Request->is('action', 'index', $controller));
ServerRequest::addDetector('add', fn(ServerRequest $Request, string|array|null $controller = null): bool => $Request->is('action', 'add', $controller));
ServerRequest::addDetector('edit', fn(ServerRequest $Request, string|array|null $controller = null): bool => $Request->is('action', 'edit', $controller));
ServerRequest::addDetector('delete', fn(ServerRequest $Request, string|array|null $controller = null): bool => $Request->is('action', 'delete', $controller));
ServerRequest::addDetector('view', fn(ServerRequest $Request, string|array|null $controller = null): bool => $Request->is('action', 'view', $controller));

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
ServerRequest::addDetector('controller', fn(ServerRequest $Request, string|array $controller): bool => in_array($Request->getParam('controller'), (array)$controller));
