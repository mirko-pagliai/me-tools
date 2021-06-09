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
 * @property \MeTools\View\Helper\IconHelper $Icon
 */
class PaginatorHelper extends CakePaginatorHelper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Form' => ['className' => 'MeTools.Form'],
        'Html' => ['className' => 'MeTools.Html'],
        'MeTools.Icon',
        'Number',
        'Url',
    ];

    /**
     * Construct the widgets and binds the default context providers.
     *
     * This method only rewrites the default templates config.
     * @param \Cake\View\View $View The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     * @return void
     */
    public function __construct(View $View, array $config = [])
    {
        $this->_defaultConfig = Hash::merge($this->_defaultConfig, ['templates' => [
            'nextActive' => '<li class="next page-item"><a class="page-link" rel="next" href="{{url}}">{{text}}</a></li>',
            'nextDisabled' => '<li class="next page-item disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
            'prevActive' => '<li class="prev page-item"><a class="page-link" rel="prev" href="{{url}}">{{text}}</a></li>',
            'prevDisabled' => '<li class="prev page-item disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
            'first' => '<li class="first page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'last' => '<li class="last page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="active page-item"><a class="page-link" href="">{{text}}</a></li>',
            'ellipsis' => '<li class="ellipsis page-item">&hellip;</li>',
        ]]);

        parent::__construct($View, $config);
    }

    /**
     * Generates a "next" link for a set of paged records
     * @param string $title Title for the link
     * @param array $options Options for pagination link
     * @return string A "next" link or a disabled link
     */
    public function next(string $title = 'Next >>', array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false, 'icon-align' => 'right']);
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::next($title, $options->toArray());
    }

    /**
     * Generates a "previous" link for a set of paged records
     * @param string $title Title for the link
     * @param array $options Options for pagination link
     * @return string A "previous" link or a disabled link
     */
    public function prev(string $title = '<< Previous', array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false]);
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::prev($title, $options->toArray());
    }
}
