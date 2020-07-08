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

use Cake\Core\Plugin;
use Cake\I18n\I18n;
use Cake\View\Helper;

/**
 * Library helper
 */
class LibraryHelper extends Helper
{
    /**
     * Helpers.
     *
     * The `Asset` helper will be loaded by the `initialize()` method. If the
     *  `Assets` plugin doesn't exist, it will be a copy of the `Html` helper.
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => 'MeTools.Html'],
    ];

    /**
     * It will contain the output code
     * @var array
     */
    protected $output = [];

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite the constructor and
     *  call parent.
     * @param array $config The configuration settings provided to this helper
     * @return void
     * @since 2.18.0
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->Asset = clone $this->Html;
        if (Plugin::getCollection()->has('Assets')) {
            $this->Asset = $this->getView()->loadHelper('Assets.Asset');
        }
    }

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
    protected function buildDatetimepicker(string $input, array $options = []): string
    {
        $this->Asset->script([
            '/vendor/moment/moment-with-locales.min',
            'MeTools.bootstrap-datetimepicker.min',
        ], ['block' => 'script_bottom']);

        $this->Asset->css(
            '/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min',
            ['block' => 'css_bottom']
        );

        $options = optionsParser($options, [
            'icons' => [
                'time' => 'fas fa-clock',
                'date' => 'fas fa-calendar',
                'up' => 'fas fa-chevron-up',
                'down' => 'fas fa-chevron-down',
                'previous' => 'fas fa-chevron-left',
                'next' => 'fas fa-chevron-right',
                'today' => 'fas fa-dot-circle',
                'clear' => 'fas fa-trash',
                'close' => 'fas fa-times',
            ],
            'locale' => substr(I18n::getLocale(), 0, 2) ?: 'en-gb',
            'showTodayButton' => true,
            'showClear' => true,
        ]);

        return sprintf('$("%s").datetimepicker(%s);', $input, json_encode($options->toArray(), JSON_PRETTY_PRINT));
    }

    /**
     * Before layout callback. beforeLayout is called before the layout is
     *  rendered
     * @return void
     * @uses MeTools\View\Helper\HtmlHelper::scriptBlock()
     * @uses $output
     */
    public function beforeLayout(): void
    {
        if (!$this->output) {
            return;
        }

        //Writes the output
        $output = array_map(function (string $output) {
            return '    ' . $output;
        }, $this->output);

        $this->Html->scriptBlock(
            sprintf('$(function() {%s});', PHP_EOL . implode(PHP_EOL, $output) . PHP_EOL),
            ['block' => 'script_bottom']
        );

        //Resets the output
        $this->output = [];
    }

    /**
     * Create a script block for Google Analytics
     * @param string $id Analytics ID
     * @uses MeTools\View\Helper\HtmlHelper::scriptBlock()
     * @return string|null A script tag or `null`
     */
    public function analytics(string $id): ?string
    {
        return $this->getView()->getRequest()->is('localhost') ? null : $this->Html->scriptBlock(
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
     * @uses MeTools\View\Helper\Html::script()
     */
    public function ckeditor(bool $jquery = false): void
    {
        is_readable_or_fail(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');

        $scripts = ['/ckeditor/ckeditor'];

        //Checks for the jQuery adapter
        if ($jquery && is_readable(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js')) {
            $scripts[] = '/ckeditor/adapters/jquery';
        }

        //Checks the init file `APP/webroot/js/ckeditor_init.php` or
        //  `APP/webroot/js/ckeditor_init.js`.
        //Otherwise uses the init file `APP/plugin/MeTools/webroot/js/ckeditor_init.js`
        $init = 'MeTools.ckeditor_init.php?type=js';
        if (is_readable(WWW_ROOT . 'js' . DS . 'ckeditor_init.php')) {
            $init = 'ckeditor_init.php?type=js';
        } elseif (is_readable(WWW_ROOT . 'js' . DS . 'ckeditor_init.js')) {
            $init = 'ckeditor_init';
        }

        $this->Html->script(array_merge($scripts, [$init]), ['block' => 'script_bottom']);
    }

    /**
     * Adds a datepicker to the `$input` field.
     *
     * To create an input field compatible with datepicker, you should use the
     *  `datepicker()` method provided by the `FormHelper`.
     *
     * Bootstrap Datepicker and Moment.js should be installed via Composer.
     * @param string|null $input Target field. Default is `.datepicker`
     * @param array $options Options for the datepicker
     * @return void
     * @see MeTools\View\Helper\FormHelper::datepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
     * @uses buildDatetimepicker()
     * @uses $output
     */
    public function datepicker(?string $input = null, array $options = []): void
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
     * @param string|null $input Target field. Default is `.datetimepicker`
     * @param array $options Options for the datetimepicker
     * @return void
     * @see MeTools\View\Helper\FormHelper::datetimepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
     * @uses buildDatetimepicker()
     * @uses $output
     */
    public function datetimepicker(?string $input = null, array $options = []): void
    {
        $this->output[] = self::buildDatetimepicker($input ?: '.datetimepicker', $options);
    }

    /**
     * Loads all fancybox files
     * @return void
     * @see https://fancyapps.com/fancybox/3 fancybox documentation
     * @uses Assets\View\Helper\AssetHelper::script()
     */
    public function fancybox(): void
    {
        $this->Html->css('/vendor/fancyapps-fancybox/jquery.fancybox.min', ['block' => 'css_bottom']);

        $scripts = ['/vendor/fancyapps-fancybox/jquery.fancybox.min'];
        //Checks the init file inside `APP/webroot/js/`.
        if (is_readable(WWW_ROOT . 'js' . DS . 'fancybox_init.js')) {
            $scripts[] = 'fancybox_init';
        }

        $this->Asset->script($scripts, ['block' => 'script_bottom']);
    }

    /**
     * Create a script block for Shareaholic.
     *
     * Note that this code only adds the Shareaholic "setup code".
     * To render the "share buttons", you have to use the `HtmlHelper`.
     * @param string $siteId Shareaholic site ID
     * @return string|null Html code
     * @uses Assets\View\Helper\AssetHelper::script()
     * @see MeTools\View\Helper\HtmlHelper::shareaholic()
     */
    public function shareaholic(string $siteId): ?string
    {
        return $this->Html->script('//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js', [
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
     * @uses $output
     */
    public function slugify(string $sourceField = 'form #title', string $targetField = 'form #slug'): void
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
     * @param string|null $input Target field. Default is `.timepicker`
     * @param array $options Options for the timepicker
     * @return void
     * @see MeTools\View\Helper\FormHelper::timepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
     * @uses buildDatetimepicker()
     * @uses $output
     */
    public function timepicker(?string $input = null, array $options = []): void
    {
        $options = optionsParser($options, ['pickTime' => false]);

        $this->output[] = self::buildDatetimepicker($input ?: '.timepicker', $options->toArray());
    }
}
