<?php
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

use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\View\Helper;

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
        ASSETS . '.Asset',
        'Html' => ['className' => ME_TOOLS . '.Html'],
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
     * @uses Assets\View\Helper\AssetHelper::css()
     * @uses Assets\View\Helper\AssetHelper::script()
     */
    protected function buildDatetimepicker($input, array $options = [])
    {
        $this->Asset->script([
            '/vendor/moment/moment-with-locales.min',
            ME_TOOLS . '.bootstrap-datetimepicker.min',
        ], ['block' => 'script_bottom']);

        $this->Asset->css(
            '/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min',
            ['block' => 'css_bottom']
        );

        //Gets the current locale
        $locale = substr(I18n::getLocale(), 0, 2);

        $options = optionsParser($options, [
            'icons' => [
                'time' => 'fa fa-clock-o',
                'date' => 'fa fa-calendar',
                'up' => 'fa fa-chevron-up',
                'down' => 'fa fa-chevron-down',
                'previous' => 'fa fa-chevron-left',
                'next' => 'fa fa-chevron-right',
                'today' => 'fa fa-dot-circle-o',
                'clear' => 'fa fa-trash',
                'close' => 'fa fa-times',
            ],
            'locale' => $locale ?: 'en-gb',
            'showTodayButton' => true,
            'showClear' => true,
        ]);

        return sprintf('$("%s").datetimepicker(%s);', $input, json_encode($options->toArray(), JSON_PRETTY_PRINT));
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
    public function beforeLayout(Event $event, $layoutFile)
    {
        //Writes the output
        if (!empty($this->output)) {
            $output = implode(PHP_EOL, array_map(function ($v) {
                return "    " . $v;
            }, $this->output));

            $this->Html->scriptBlock(
                sprintf('$(function() {%s});', PHP_EOL . $output . PHP_EOL),
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
     * @return mixed|null Html code
     */
    public function analytics($id)
    {
        if ($this->request->is('localhost')) {
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
     * @param bool $jquery `true` if you want to use the jQuery adapter
     * @return void
     * @see MeTools\View\Helper\FormHelper::ckeditor()
     * @see http://docs.cksource.com CKEditor documentation
     * @uses MeTools\View\Helper\Html::js()
     */
    public function ckeditor($jquery = false)
    {
        if (!is_readable(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js')) {
            return;
        }

        $scripts = ['/ckeditor/ckeditor'];

        //Checks for the jQuery adapter
        if ($jquery && is_readable(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js')) {
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
     * @uses buildDatetimepicker()
     */
    public function datepicker($input = null, array $options = [])
    {
        $options = optionsParser($options, ['format' => 'YYYY/MM/DD']);

        $this->output[] = self::buildDatetimepicker($input ?: '.datepicker', $options->toArray());
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
     * @uses buildDatetimepicker()
     */
    public function datetimepicker($input = null, array $options = [])
    {
        $this->output[] = self::buildDatetimepicker($input ?: '.datetimepicker', $options);
    }

    /**
     * Loads all FancyBox scripts.
     *
     * FancyBox must be installed via Composer.
     * @return void
     * @see http://fancyapps.com/fancybox/#docs FancyBox documentation
     * @uses Assets\View\Helper\AssetHelper::script()
     */
    public function fancybox()
    {
        $this->Html->css([
            '/vendor/fancybox/jquery.fancybox',
            '/vendor/fancybox/helpers/jquery.fancybox-buttons',
            '/vendor/fancybox/helpers/jquery.fancybox-thumbs',
        ], ['block' => 'css_bottom']);

        $scripts = [
            '/vendor/fancybox/jquery.fancybox.pack',
            '/vendor/fancybox/helpers/jquery.fancybox-buttons',
            '/vendor/fancybox/helpers/jquery.fancybox-thumbs',
        ];

        //Checks for `APP/webroot/js/`
        if (is_readable(WWW_ROOT . 'js' . DS . 'fancybox_init.js')) {
            $scripts[] = 'fancybox_init';
        //Else, uses `APP/plugin/MeTools/webroot/fancybox/`
        } else {
            $scripts[] = 'MeTools./fancybox/fancybox_init';
        }

        $this->Asset->script($scripts, ['block' => 'script_bottom']);
    }

    /**
     * Create a script block for Shareaholic.
     *
     * Note that this code only adds the Shareaholic "setup code".
     * To render the "share buttons", you have to use the `HtmlHelper`.
     * @param string $siteId Shareaholic site ID
     * @return mixed Html code
     * @uses Assets\View\Helper\AssetHelper::script()
     * @see MeTools\View\Helper\HtmlHelper::shareaholic()
     */
    public function shareaholic($siteId)
    {
        return $this->Html->js('//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js', [
            'async' => 'async',
            'block' => 'script_bottom',
            'data-cfasync' => 'false',
            'data-shr-siteid' => $siteId,
        ]);
    }

    /**
     * Through `slugify.js`, it provides the slug of a field.
     *
     * It reads the value of the `$sourceField` field and it sets its slug in
     *  the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
     * @return void
     * @uses Assets\View\Helper\AssetHelper::script()
     * @uses output
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug')
    {
        $this->Asset->script('MeTools.slugify', ['block' => 'script_bottom']);

        $this->output[] = sprintf('$().slugify("%s", "%s");', $sourceField, $targetField);
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
     * @uses buildDatetimepicker()
     */
    public function timepicker($input = null, array $options = [])
    {
        $options = optionsParser($options, ['pickTime' => false]);

        $this->output[] = self::buildDatetimepicker($input ?: '.timepicker', $options->toArray());
    }
}
