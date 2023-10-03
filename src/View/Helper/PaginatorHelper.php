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
namespace MeTools\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper\PaginatorHelper as CakePaginatorHelper;
use Cake\View\View;

/**
 * Provides functionalities to the generation of pagers
 * @property \MeTools\View\Helper\HtmlHelper $Html
 * @property \MeTools\View\Helper\IconHelper $Icon
 * @property \Cake\View\Helper\NumberHelper $Number
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class PaginatorHelper extends CakePaginatorHelper
{
    /**
     * @array
     */
    public $helpers = [
        'MeTools.Html',
        'MeTools.Icon',
        'Number',
        'Url',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(View $View, array $config = [])
    {
        /** @see \Cake\View\Helper\PaginatorHelper::$_defaultConfig */
        $this->_defaultConfig = Hash::merge($this->_defaultConfig, ['templates' => [
            'nextActive' => '<li class="page-item"><a class="page-link" rel="next" href="{{url}}">{{text}}</a></li>',
            'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="#">{{text}}</a></li>',
            'prevActive' => '<li class="page-item"><a class="page-link" rel="prev" href="{{url}}">{{text}}</a></li>',
            'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="#">{{text}}</a></li>',
            'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="page-item active" aria-current="page"><a class="page-link" href="#">{{text}}</a></li>',
            'sort' => '<a href="{{url}}" class="text-decoration-none">{{text}}</a>',
            'sortAsc' => '<a class="asc text-decoration-none" href="{{url}}">{{text}} <i class="fa-solid fa-arrow-down-short-wide"></i></a>',
            'sortDesc' => '<a class="desc text-decoration-none" href="{{url}}">{{text}} <i class="fa-solid fa-arrow-down-wide-short"></i></a>',
        ]]);

        parent::__construct($View, $config);
    }

    /**
     * @inheritDoc
     */
    public function next(string $title = 'Next >>', array $options = []): string
    {
        $options += ['escape' => false, 'icon-align' => 'right'];
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::next($title, $options);
    }

    /**
     * @inheritDoc
     */
    public function prev(string $title = '<< Previous', array $options = []): string
    {
        $options += ['escape' => false];
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::prev($title, $options);
    }
}
