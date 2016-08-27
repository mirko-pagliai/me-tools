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
     * Creates a badge, according to Bootstrap.
     * @param string $text Badge text
     * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see http://getbootstrap.com/components/#badges Bootstrap documentation
     * @uses tag()
     */
    public function badge($text, array $options = [])
    {
        $options = optionValues(['class' => 'badge'], $options);

        return self::tag('span', $text, $options);
    }

    /**
     * Ends capturing output for a CSS block.
     *
     * To start capturing output, see the `cssStart()` method.
     * @return void
     * @see cssStart()
     * @uses Cake\View\ViewBlock::end()
     */
    public function cssEnd()
    {
        $this->_View->end();
    }

    /**
     * Starts capturing output for a CSS block.
     *
     * To end capturing output, see the `cssEnd()` method.
     * @return void
     * @see cssEnd()
     * @uses Cake\View\ViewBlock::start()
     */
    public function cssStart()
    {
        $this->_View->start('css');
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
     * @param string $text heading content
     * @param array $options Array of options and HTML attributes
     * @return string Html code
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
     * Create an `iframe` element.
     *
     * You can use `$ratio` to create a responsive embed.
     * @param string $url Url for the iframe
     * @param array $options Array of options and HTML attributes
     * @param string $ratio Ratio (`16by9` or `4by3`)
     * @return string Html code
     * @see http://getbootstrap.com/components/#responsive-embed Responsive embed
     * @uses tag()
     * @uses div()
     */
    public function iframe($url, array $options = [], $ratio = false)
    {
        $options['src'] = $url;

        if ($ratio === '16by9' || $ratio === '4by3') {
            $options = optionValues(['class' => 'embed-responsive-item'], $options);
            
            return self::div(
                sprintf('embed-responsive embed-responsive-%s', $ratio),
                self::tag('iframe', ' ', $options)
            );
        }

        return self::tag('iframe', ' ', $options);
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
     * @return string Html code
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
     * @return string Html code
     * @see MeTools\View\Helper\LayoutHelper::shareaholic()
     * @uses div()
     */
    public function shareaholic($appId)
    {
        return $this->div('shareaholic-canvas', ' ', [
            'data-app' => 'share_buttons',
            'data-app-id' => $appId,
        ]);
    }

    /**
     * Returns a formatted block tag.
     * @param string $name Tag name
     * @param string $text Tag content. If null, only a start tag will be
     *  printed
     * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @uses addIcon()
     */
    public function tag($name, $text = null, array $options = [])
    {
        if (!empty($options['tooltip'])) {
            $options = optionValues(['data-toggle' => 'tooltip'], $options);
            $options = optionDefaults(['title' => $options['tooltip']], $options);
        }

        return parent::tag($name, $text, $options);
    }

    /**
     * Adds the `viewport` meta tag. By default, it uses options as required
     *  by Bootstrap
     * @param array $options Attributes for the generated tag. If the type
     *  attribute is html, rss, atom, or icon, the mime-type is returned
     * @return string Html code
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
     * You can use `$ratio` to create a responsive embed.
     * @param string $id YouTube video ID
     * @param array $options Array of options and HTML attributes
     * @param string $ratio Ratio (`16by9` or `4by3`)
     * @return string Html code
     * @uses iframe()
     */
    public function youtube($id, array $options = [], $ratio = '16by9')
    {
        $options = optionDefaults([
            'allowfullscreen' => 'allowfullscreen',
            'height' => 480,
            'width' => 640,
        ], $options);

        return self::iframe(
            sprintf('https://www.youtube.com/embed/%s', $id),
            $options,
            $ratio
        );
    }
}
