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
 * @since       2.25.0
 */

namespace MeTools\Controller;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;

/**
 * Application Controller
 * @deprecated 2.25.5 Deprecated. Your `AppController` should directly extend `Cake\Controller\Controller`
 * @codeCoverageIgnore
 **/
abstract class AppController extends Controller
{
    /**
     * @inheritDoc
     */
    public function __construct(?ServerRequest $request = null, ?Response $response = null, ?string $name = null, ?EventManagerInterface $eventManager = null, ?ComponentRegistry $components = null)
    {
        deprecationWarning('Deprecated. Your `AppController` should directly extend `Cake\Controller\Controller`');

        parent::__construct($request, $response, $name, $eventManager, $components);
    }
}
