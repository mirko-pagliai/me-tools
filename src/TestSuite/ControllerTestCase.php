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
 * @since       2.21.2
 */
namespace MeTools\TestSuite;

use Cake\ORM\Association;
use Cake\Utility\Inflector;
use MeTools\TestSuite\TestCase;

class ControllerTestCase extends TestCase
{
    /**
     * Magic method, provides access to undefined properties
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get(string $name)
    {
        switch ($name) {
//            case 'Table':
//                return $this->Table ??= $this->getTableLocator()->get($this->alias);
//            case 'alias':
//                return $this->alias ??= $this->getAlias($this);
//            case 'belongsTo':
//                return $this->belongsTo ??= array_filter(iterator_to_array($this->Table->associations()), fn(Association $association): bool => $association instanceof BelongsTo);
//            case 'controller':
//                if (empty($this->controller)) {
//                    /** @var class-string<\Cake\Controller\Controller> $controller */
//                    $controller = $this->getOriginClassNameOrFail($this);
//                    $this->controller = $controller;
//                }
//
//                return $this->controller;
//            case 'url':
//                return $this->url ??= '/' . Inflector::dasherize($this->alias);
            default:
                $this->fail('Unknown `$' . $name . '` property');
        }
    }
}
