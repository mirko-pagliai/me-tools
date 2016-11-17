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
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;
use MeTools\Core\Plugin;

/**
 * Library helper
 */
class LibraryHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => 'MeTools.Html'],
        'Assets.Asset',
    ];

    /**
     * It will contain the output code
     * @var array
     */
    protected $output = [];

    /**
     * Internal function to generate datepicker and timepicker.
     *
     * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field
     * @param array $options Options for the datepicker
     * @return string jQuery code
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
     * @uses MeTools\View\Helper\AssetHelper::css()
     * @uses MeTools\View\Helper\AssetHelper::js()
     */
    protected function _datetimepicker($input, array $options = [])
    {
        $this->Asset->script([
            '/vendor/moment/moment-with-locales.min',
            '/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min',
        ], ['block' => 'script_bottom']);

        $this->Asset->css(
            '/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min',
            ['block' => 'css_bottom']
        );

        $options = optionDefaults([
            'showTodayButton' => true,
            'showClear' => true,
        ], $options);

        if (empty($options['icons'])) {
            $options['icons'] = [
                'time' => 'fa fa-clock-o',
                'date' => 'fa fa-calendar',
                'up' => 'fa fa-arrow-up',
                'down' => 'fa fa-arrow-down',
                'previous' => 'fa fa-arrow-left',
                'next' => 'fa fa-arrow-right',
                'today' => 'fa fa-dot-circle-o',
                'clear' => 'fa fa-trash',
            ];
        }

        //Sets the current locale
        $locale = substr(\Cake\I18n\I18n::locale(), 0, 2);
        $options = optionDefaults([
            'locale' => empty($locale) ? 'en-gb' : $locale,
        ], $options);

        return sprintf(
            '$("%s").datetimepicker(%s);',
            $input,
            json_encode($options, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Before layout callback. beforeLayout is called before the layout is
     *  rendered.
     * @param \Cake\Event\Event $event An Event instance
     * @param string $layoutFile The layout about to be rendered
     * @return void
     * @uses MeTools\View\Helper\HtmlHelper::scriptBlock()
     * @uses output
     */
    public function beforeLayout(\Cake\Event\Event $event, $layoutFile)
    {
        //Writes the output
        if (!empty($this->output)) {
            $this->output = implode(PHP_EOL, array_map(function ($v) {
                return "\t" . $v;
            }, $this->output));

            $this->Html->scriptBlock(
                sprintf('$(function() {%s});', PHP_EOL . $this->output . PHP_EOL),
                ['block' => 'script_bottom']
            );

            //Resets the output
            $this->output = [];
        }
    }

    /**
     * Create a script block for Google Analytics
     * @param string $id Analytics ID
     * @uses MeTools\View\Helper\HtmlHelper::scriptBlock()
     * @return mixed|null Html code or null if is localhost
     */
    public function analytics($id)
    {
        if (isLocalhost()) {
            return;
        }

        return $this->Html->scriptBlock(
            sprintf('!function(e,a,t,n,c,o,s){e.GoogleAnalyticsObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=a.createElement(t),s=a.getElementsByTagName(t)[0],o.async=1,o.src=n,s.parentNode.insertBefore(o,s)}(window,document,"script","//www.google-analytics.com/analytics.js","ga"),ga("create","%s","auto"),ga("send","pageview");', $id),
            ['block' => 'script_bottom']
        );
    }

    /**
     * Loads all CKEditor scripts.
     *
     * To know how to install and configure CKEditor, please refer to the
     *  `README.md` file.
     *
     * CKEditor must be located into `APP/webroot/ckeditor`.
     *
     * To create an input field for CKEditor, you should use the `ckeditor()`
     *  method provided by the `FormHelper`.
     * @param bool $jquery false if you don't want to use the jQuery adapter
     * @return void
     * @see MeTools\View\Helper\FormHelper::ckeditor()
     * @see http://docs.cksource.com CKEditor documentation
     * @uses MeTools\View\Helper\Html::js()
     */
    public function ckeditor($jquery = true)
    {
        $path = WWW_ROOT . 'ckeditor' . DS;

        if (!is_readable($path . 'ckeditor.js')) {
            return;
        }

        $scripts = ['/ckeditor/ckeditor'];

        //Checks for the jQuery adapter
        if ($jquery && is_readable($path . 'adapters' . DS . 'jquery.js')) {
            $scripts[] = '/ckeditor/adapters/jquery';
        }

        //Checks for `APP/webroot/js/ckeditor_init.php`
        if (is_readable(WWW_ROOT . 'js' . DS . 'ckeditor_init.php')) {
            $scripts[] = 'ckeditor_init.php?';
        //Checks for `APP/webroot/js/ckeditor_init.js`
        } elseif (is_readable(WWW_ROOT . 'js' . DS . 'ckeditor_init.js')) {
            $scripts[] = 'ckeditor_init';
        //Else, uses `APP/plugin/MeTools/webroot/js/ckeditor_init.js`
        } else {
            $scripts[] = 'MeTools.ckeditor_init.php?';
        }

        $this->Html->js($scripts, ['block' => 'script_bottom']);
    }

    /**
     * Adds a datepicker to the `$input` field.
     *
     * To create an input field compatible with datepicker, you should use the
     *  `datepicker()` method provided by the `FormHelper`.
     *
     * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field. Default is `.datepicker`
     * @param array $options Options for the datepicker
     * @return void
     * @see MeTools\View\Helper\FormHelper::datepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
     * @uses output
     * @uses _datetimepicker()
     */
    public function datepicker($input = null, array $options = [])
    {
        $input = empty($input) ? '.datepicker' : $input;

        $options = optionDefaults(['format' => 'YYYY/MM/DD'], $options);

        $this->output[] = self::_datetimepicker($input, $options);
    }

    /**
     * Adds a datetimepicker to the `$input` field.
     *
     * To create an input field compatible with datetimepicker, you should use
     *  the `datetimepicker()` method provided by the `FormHelper`.
     * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field. Default is `.datetimepicker`
     * @param array $options Options for the datetimepicker
     * @return void
     * @see MeTools\View\Helper\FormHelper::datetimepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
     * @uses output
     * @uses _datetimepicker()
     */
    public function datetimepicker($input = null, array $options = [])
    {
        $input = empty($input) ? '.datetimepicker' : $input;

        $this->output[] = self::_datetimepicker($input, $options);
    }

    /**
     * Loads all FancyBox scripts.
     *
     * FancyBox must be installed via Composer.
     * @return void
     * @see http://fancyapps.com/fancybox/#docs FancyBox documentation
     * @uses MeTools\View\Helper\AssetHelper::js()
     * @uses MeTools\Core\Plugin::path()
     */
    public function fancybox()
    {
        $this->Html->css([
            '/vendor/fancybox/jquery.fancybox',
            '/vendor/fancybox/helpers/jquery.fancybox-buttons',
            '/vendor/fancybox/helpers/jquery.fancybox-thumbs',
        ], ['block' => 'css_bottom']);

        $this->Asset->script([
            '/vendor/fancybox/jquery.fancybox.pack',
            '/vendor/fancybox/helpers/jquery.fancybox-buttons',
            '/vendor/fancybox/helpers/jquery.fancybox-thumbs',
        ], ['block' => 'script_bottom']);

        //Checks for the init script into `APP/webroot/js/`
        if (is_readable(WWW_ROOT . 'js' . DS . 'fancybox_init.js')) {
            $script = 'fancybox_init';
        //Else, checks for the init script into
        //  `APP/plugin/MeTools/webroot/fancybox/`
        } elseif (Plugin::path(METOOLS, 'webroot' . DS . 'fancybox' . DS . 'fancybox_init.js', true)) {
            $script = 'MeTools./fancybox/fancybox_init';
        } else {
            return;
        }

        $this->Asset->script($script, ['block' => 'script_bottom']);
    }

    /**
     * Create a script block for Shareaholic.
     *
     * Note that this code only adds the Shareaholic "setup code".
     * To render the "share buttons", you have to use the `HtmlHelper`.
     * @param string $siteId Shareaholic site ID
     * @return mixed Html code
     * @see MeTools\View\Helper\HtmlHelper::shareaholic()
     * @uses MeTools\View\Helper\HtmlHelper::js()
     */
    public function shareaholic($siteId)
    {
        return $this->Html->js(
            '//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js',
            [
                'async' => 'async',
                'block' => 'script_bottom',
                'data-cfasync' => 'false',
                'data-shr-siteid' => $siteId,
            ]
        );
    }

    /**
     * Through `slugify.js`, it provides the slug of a field.
     *
     * It reads the value of the `$sourceField` field and it sets its slug in
     *  the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
     * @return void
     * @uses MeTools\View\Helper\AssetHelper::js()
     * @uses output
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug')
    {
        $this->Asset->script('MeTools.slugify', ['block' => 'script_bottom']);

        $this->output[] = sprintf(
            '$().slugify("%s", "%s");',
            $sourceField,
            $targetField
        );
    }

    /**
     * Adds a timepicker to the `$input` field.
     *
     * To create an input field compatible with datepicker, you should use the
     *  `timepicker()` method provided by the `FormHelper`.
     *
     * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string $input Target field. Default is `.timepicker`
     * @param array $options Options for the timepicker
     * @return void
     * @see MeTools\View\Helper\FormHelper::timepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
     * @uses output
     * @uses _datetimepicker()
     */
    public function timepicker($input = null, array $options = [])
    {
        $input = empty($input) ? '.timepicker' : $input;

        $options = optionDefaults(['pickTime' => false], $options);

        $this->output[] = self::_datetimepicker($input, $options);
    }
}
