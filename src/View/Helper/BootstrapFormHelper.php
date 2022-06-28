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
use Cake\View\Helper\FormHelper;
use Cake\View\View;

/**
 * Provides functionalities for forms
 * @property \MeTools\View\Helper\IconHelper $Icon
 * @property \MeTools\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class BootstrapFormHelper extends FormHelper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => 'MeTools.Html'],
        'MeTools.Icon',
        'Url',
    ];

    /**
     * Construct the widgets and binds the default context providers.
     *
     * This method only rewrites the default templates config.
     * @param \Cake\View\View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     */
    public function __construct(View $view, array $config = [])
    {
        //Rewrites default templates config
        $this->_defaultConfig = Hash::merge($this->_defaultConfig, ['templates' => [
            //Container element user for checkboxes
            'checkboxContainer' => '<div class="input mb-3 form-check{{required}}">{{content}}{{help}}</div>',
            //Container element used by `control()`
            'inputContainer' => '<div class="input mb-3 {{type}}{{required}}">{{content}}{{help}}</div>',
            //Container element used by `control()` when a field has an error
            'inputContainerError' => '<div class="input mb-3 {{type}}{{required}} error">{{content}}{{help}}{{error}}</div>',
            // Submit/reset button
            'inputSubmit' => '<button{{attrs}}>{{text}}</button>',
        ]]);

        parent::__construct($view, $config);
    }

    /**
     * Creates a `<button>` tag.
     *
     * See the parent method for all available options.
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string A HTML button tag
     */
    public function button(string $title, array $options = []): string
    {
        $options = optionsParser($options, ['escapeTitle' => false, 'type' => 'button']);
        $options->addButtonClasses($options->contains('type', 'submit') ? 'success' : 'primary');
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::button($title, $options->toArray());
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * See the parent method for all available options.
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array<string, mixed> $options Each type of input takes different options
     * @return string Completed form widget
     */
    public function control(string $fieldName, array $options = []): string
    {
        $this->resetTemplates();
        $options = optionsParser($options);

        //If the name contains the "password" word, then the type is `password`
        if (str_contains($fieldName, 'password')) {
            $options->addDefault(['type' => 'password']);
        }

        $type = $options->get('type') ?? $this->_inputType($fieldName, $options->toArray());

        $options->append('class', $type == 'checkbox' ? 'form-check-input' : 'form-control');

        if ($this->isFieldError($fieldName)) {
            $options->append('class', 'is-invalid');
        } elseif ($this->getView()->getRequest()->is('post')) {
            $options->append('class', 'is-valid');
        }

        //Help text (form text)
        //See https://getbootstrap.com/docs/5.2/forms/overview/#form-text
        if ($options->exists('help')) {
            $help = implode('', array_map(fn(string $help): string => $this->Html->div('form-text text-muted', trim($help)), (array)$options->consume('help')));
            $options->append('templateVars', compact('help'));
        }

        //Input group
        //See https://getbootstrap.com/docs/5.2/forms/input-group
        if ($options->exists('append-text') || $options->exists('prepend-text')) {
            $this->setTemplates(['formGroup' => '{{label}}<div class="input-group">{{prependText}}{{input}}{{appendText}}</div>']);
            $appendText = $options->exists('append-text') ? $this->Html->span($options->consume('append-text'), ['class' => 'input-group-text']) : '';
            $prependText = $options->exists('prepend-text') ? $this->Html->span($options->consume('prepend-text'), ['class' => 'input-group-text']) : '';
            $options->append('templateVars', compact('appendText', 'prependText'));
        }

        return parent::control($fieldName, $options->toArray());
    }

    /**
     * Returns a formatted LABEL element for HTML forms.
     *
     * Will automatically generate a `for` attribute if one is not provided.
     *
     * See the parent method for all available options.
     * @param string $fieldName This should be "modelname.fieldname"
     * @param string|null $text Text that will appear in the label field. If
     *   $text is left undefined the text will be inflected from the
     *   fieldName
     * @param array<string, mixed> $options An array of HTML attributes
     * @return string The formatted LABEL element
     */
    public function label(string $fieldName, ?string $text = null, array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false]);
        $options->append('class', 'form-label fw-bolder');
        [$text, $options] = $this->Icon->addIconToText($text, $options);

        return parent::label($fieldName, $text, $options->toArray());
    }

    /**
     * Creates a "POST button", which is a "POST link" with all the appearance
     *  of a button
     * @param string $title The content to be wrapped by <a> tags
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array<string, mixed> $options Array of HTML attributes
     * @return string An `<a />` element
     */
    public function postButton(string $title = '', $url = null, array $options = []): string
    {
        $options = optionsParser($options)->add('role', 'button')->addButtonClasses();

        return $this->postLink($title, $url, $options->toArray());
    }

    /**
     * Creates a submit button element. This method will generate `<input />`
     *  elements that can be used to submit, and reset forms by using $options.
     *  Image submits can be created by supplying an image path for $caption.
     *
     * See the parent method for all available options.
     * @param string|null $caption The label appearing on the button OR if string contains :// or the
     *  extension .jpg, .jpe, .jpeg, .gif, .png use an image if the extension
     *  exists, AND the first character is /, image is relative to webroot,
     *  OR if the first character is not /, image is relative to webroot/img
     * @param array<string, mixed> $options Array of options
     * @return string A HTML submit button
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false, 'type' => 'submit']);
        $options->addButtonClasses($options->contains('type', 'submit') ? 'success' : 'primary');
        [$text, $options] = $this->Icon->addIconToText($caption, $options);
        $options->append('templateVars', compact('text'));

        return parent::submit($caption, $options->toArray());
    }
}
