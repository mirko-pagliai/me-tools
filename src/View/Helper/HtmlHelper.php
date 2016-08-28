<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 * @see         http://api.cakephp.org/3.3/class-Cake.View.Helper.HtmlHelper.html HtmlHelper
 */
namespace MeTools\View\Helper;

use MeTools\View\Helper\BaseHtmlHelper;

/**
 * Provides functionalities for HTML code.
 *
 * This class override `Cake\View\Helper\HtmlHelper` and improve its methods,
 * adding extra methods that `BaseHtmlHelper` does not provide
 *
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = ['Html' => ['className' => 'MeTools.BaseHtml']];
 * </code>
 */
class HtmlHelper extends BaseHtmlHelper
{
    /**
     * Creates a badge, according to Bootstrap
     * @param string $text Badge text
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see http://getbootstrap.com/components/#badges Bootstrap documentation
     * @uses tag()
     */
    public function badge($text, array $options = [])
    {
        $options = optionValues(['class' => 'badge'], $options);

        return self::tag('span', $text, $options);
    }

    /**
     * Creates an heading.
     *
     * This method is useful if you want to create an heading with a secondary
     *  text, according to Bootstrap.
     * In this case you have to use the `small` option.
     *
     * By default, this method creates an `<h2>` tag. To create a different
     *  tag, you have to use the `type` option.
     * @param string $text Heading content
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see http://getbootstrap.com/css/#type-headings Bootstrap documentation
     * @uses small()
     * @uses tag()
     */
    public function heading($text, array $options = [])
    {
        if (empty($options['type']) || !preg_match('/^h[1-6]$/', $options['type'])) {
            $type = 'h2';
        } else {
            $type = $options['type'];
        }

        if (!empty($options['small']) && is_string($options['small'])) {
            $text = sprintf('%s %s', $text, self::small($options['small']));
        }

        unset($options['type'], $options['small']);

        return self::tag($type, $text, $options);
    }

    /**
     * Create an `<iframe>` element.
     *
     * You can use the `$ratio` option (valid values: `16by9` or `4by3`) to
     *  create a responsive embed.
     * @param string $url Url for the iframe
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see http://getbootstrap.com/components/#responsive-embed Responsive embed
     * @uses tag()
     * @uses div()
     */
    public function iframe($url, array $options = [])
    {
        if ($options['ratio'] === '16by9' || $options['ratio'] === '4by3') {
            $options = optionValues(['class' => 'embed-responsive-item'], $options);
                        
            return self::div(
                sprintf('embed-responsive embed-responsive-%s', $options['ratio']),
                self::tag('iframe', $url, $options)
            );
        }

        return self::tag('iframe', $url, $options);
    }

    /**
     * Create a label, according to the Bootstrap component.
     *
     * This method creates only a label element. Not to be confused with the
     *  `label()` method provided by the `Formhelper`, which creates a label
     *  for a form input.
     *
     * Supported type are: `default`, `primary`, `success`, `info`, `warning`
     *  and `danger`.
     * @param string $text Label text
     * @param array $options HTML attributes of the list tag
     * @param string $type Label type
     * @return string
     * @see http://getbootstrap.com/components/#labels Bootstrap documentation
     * @uses tag()
     */
    public function label($text, array $options = [], $type = 'default')
    {
        $options = optionValues('class', ['label', sprintf('label-%s', $type)], $options);

        return self::tag('span', $text, $options);
    }

    /**
     * Returns the Shareaholic "share buttons".
     *
     * Note that this code only renders the Shareaholic "share button".
     * To add the "setup code", you have to use the `LayoutHelper`.
     * @param string $appId Shareaholic app ID
     * @return string
     * @see MeTools\View\Helper\LayoutHelper::shareaholic()
     * @uses div()
     */
    public function shareaholic($appId)
    {
        return self::div('shareaholic-canvas', ' ', [
            'data-app' => 'share_buttons',
            'data-app-id' => $appId,
        ]);
    }

    /**
     * Adds the `viewport` meta tag. By default, it uses options as required
     *  by Bootstrap
     * @param array $options Attributes for the generated tag. If the type
     *  attribute is html, rss, atom, or icon, the mime-type is returned
     * @return string
     * @see http://getbootstrap.com/css/#overview-mobile Bootstrap documentation
     * @uses meta()
     */
    public function viewport(array $options = [])
    {
        $default = [
            'initial-scale' => '1',
            'maximum-scale' => '1',
            'user-scalable' => 'no',
            'width' => 'device-width',
        ];

        $content = http_build_query(am($default, $options), null, ', ');

        return self::meta(am(['name' => 'viewport'], compact('content')));
    }

    /**
     * Adds a YouTube video.
     *
     * You can use the `$ratio` option (valid values: `16by9` or `4by3`) to
     *  create a responsive embed.
     * @param string $id YouTube video ID
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses iframe()
     */
    public function youtube($id, array $options = [])
    {
        $url = sprintf('https://www.youtube.com/embed/%s', $id);
        
        $options = optionDefaults([
            'allowfullscreen' => 'allowfullscreen',
            'height' => 480,
            'ratio' => '16by9',
            'width' => 640,
        ], $options);

        return self::iframe($url, $options);
    }
}
