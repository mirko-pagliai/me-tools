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
 * @see         http://getbootstrap.com/components/#breadcrumbs Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\BreadcrumbsHelper as CakeBreadcrumbsHelper;

/**
 * Creates breadcrumbs, according to the Bootstrap component
 */
class BreadcrumbsHelper extends CakeBreadcrumbsHelper
{
    /**
     * Add a crumb to the end of the trail
     * @param string|array $title If provided as a string, it represents the
     *  title of the crumb. Alternatively you can provide an array, with each
     *  values being a single crumb. Arrays are expected to be of this form:
     *  - *title* The title of the crumb
     *  - *link* The link of the crumb
     *  - *options* Options of the crumb
     * @param string|array|null $url URL of the crumb. Either a string, an array
     *  of route params to pass to Url::build() or null/empty
     * @param array $options Array of options. These options will be used as
     *  attributes HTML attribute the crumb will be rendered in (a <li> tag by
     *  default). It accepts two special keys:
     *  - *innerAttrs*: An array that allows you to define attributes for the
     *      inner element of the crumb (by default, to the link)
     *  - *templateVars*: Specific template vars in case you override the
     *  templates provided
     * @return \Cake\View\Helper\BreadcrumbsHelper
     * @since 2.16.0
     */
    public function add($title, $url = null, array $options = []): CakeBreadcrumbsHelper
    {
        $options = optionsParser($options)->append('class', 'breadcrumb-item');

        return parent::add($title, $url, $options->toArray());
    }

    /**
     * Prepend a crumb to the start of the queue
     * @param string|array $title If provided as a string, it represents the
     *  title of the crumb. Alternatively you can provide an array, with each
     *  values being a single crumb. Arrays are expected to be of this form:
     *  - *title* The title of the crumb
     *  - *link* The link of the crumb
     *  - *options* Options of the crumb
     * @param string|array|null $url URL of the crumb. Either a string, an array
     *  of route params to pass to Url::build() or null/empty
     * @param array $options Array of options. These options will be used as
     *  attributes HTML attribute the crumb will be rendered in (a <li> tag by
     *  default). It accepts two special keys:
     *  - *innerAttrs*: An array that allows you to define attributes for the
     *      inner element of the crumb (by default, to the link)
     *  - *templateVars*: Specific template vars in case you override the
     *  templates provided
     * @return \Cake\View\Helper\BreadcrumbsHelper
     * @since 2.16.0
     */
    public function prepend($title, $url = null, array $options = []): CakeBreadcrumbsHelper
    {
        $options = optionsParser($options)->append('class', 'breadcrumb-item');

        return parent::prepend($title, $url, $options->toArray());
    }

    /**
     * Renders the breadcrumbs trail
     * @param array $attributes Array of attributes applied to the `wrapper`
     *  template
     * @param array $separator Array of attributes for the `separator` template
     * @return string The breadcrumbs trail
     */
    public function render(array $attributes = [], array $separator = []): string
    {
        if (!$this->crumbs) {
            return parent::render($attributes, $separator);
        }

        //Removes the url for the last crumb
        end($this->crumbs);
        $last = key($this->crumbs);
        $this->crumbs[$last]['url'] = null;

        $attributes = optionsParser($attributes)->append('class', 'breadcrumb');

        return parent::render($attributes->toArray(), $separator);
    }
}
