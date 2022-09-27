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
namespace MeTools\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget;

/**
 * Hidden input class
 */
class HiddenWidget extends BasicWidget
{
    /**
     * Render a hidden widget
     * @param array $data The data to build an input with
     * @param \Cake\View\Form\ContextInterface $context The current form context
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
    {
        $data = optionsParser($data, [
            'name' => '',
            'val' => null,
            'type' => 'text',
            'escape' => true,
            'templateVars' => [],
        ]);
        $data->add('value', $data->consume('val'));

        return $this->_templates->format('hidden', [
            'name' => $data->get('name'),
            'type' => $data->get('type'),
            'templateVars' => $data->get('templateVars'),
            'attrs' => $this->_templates->formatAttributes($data->toArray(), ['name', 'type']),
        ]);
    }
}
