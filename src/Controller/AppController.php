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
 * @since       2.25.1
 */

namespace MeTools\Controller;

use Cake\Controller\Controller;

/**
 * Application Controller
 * @property \MeTools\Controller\Component\FlashComponent $Flash
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 * @codeCoverageIgnore
 **/
abstract class AppController extends Controller
{
    /**
     * Initialization hook method
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('MeTools.Flash');
    }
}
