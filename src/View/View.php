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

namespace MeTools\View;

use Cake\View\View as CakeView;
use MeTools\View\Helper\FormHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * View class
 * @property \MeTools\View\Helper\DropdownHelper $Dropdown
 * @property \MeTools\View\Helper\FormHelper $Form
 * @property \MeTools\View\Helper\HtmlHelper $Html
 * @property \MeTools\View\Helper\IconHelper $Icon
 * @property \MeTools\View\Helper\PaginatorHelper $Paginator
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 * @codeCoverageIgnore
 */
class View extends CakeView
{
    /**
     * Initialization hook method
     * @return void
     */
    public function initialize(): void
    {
        $this->loadHelper('MeTools.Icon');
        $this->loadHelper('Html', ['className' => HtmlHelper::class]);
        $this->loadHelper('Form', ['className' => FormHelper::class]);
        $this->loadHelper('MeTools.Paginator');
        $this->loadHelper('MeTools.Dropdown');
    }
}
