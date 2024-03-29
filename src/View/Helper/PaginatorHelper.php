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

use Cake\Core\Exception\CakeException;
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
     * @inheritDoc
     */
    protected array $helpers = [
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
            'nextActive' => '<li class="page-item"><a class="page-link" rel="next" href="{{url}}">{{text}} <i class="fa-solid fa-caret-right"></i></a></li>',
            'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="#">{{text}} <i class="fa-solid fa-caret-right"></i></a></li>',
            'prevActive' => '<li class="page-item"><a class="page-link" rel="prev" href="{{url}}"><i class="fa-solid fa-caret-left"></i> {{text}}</a></li>',
            'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="#"><i class="fa-solid fa-caret-left"></i> {{text}}</a></li>',
            'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="page-item active" aria-current="page"><a class="page-link" href="#">{{text}}</a></li>',
            'sort' => '<a href="{{url}}" class="text-decoration-none">{{text}}</a>',
            'sortAsc' => '<a class="asc text-decoration-none" href="{{url}}">{{text}} <i class="fa-solid fa-arrow-up-short-wide"></i></a>',
            'sortDesc' => '<a class="desc text-decoration-none" href="{{url}}">{{text}} <i class="fa-solid fa-arrow-down-wide-short"></i></a>',
        ]]);

        parent::__construct($View, $config);
    }

    /**
     * Returns `true` if the pagination instance is not empty.
     *
     * This prevents that a call to `paginated()` throws an exception, returning `false` instead
     * @return bool
     * @since 3.0.0
     */
    public function hasPaginated(): bool
    {
        try {
            return (bool)$this->paginated();
        } catch (CakeException) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function next(string $title = '', array $options = []): string
    {
        return parent::next($title, $options);
    }

    /**
     * @inheritDoc
     */
    public function prev(string $title = '', array $options = []): string
    {
        return parent::prev($title, $options);
    }
}
