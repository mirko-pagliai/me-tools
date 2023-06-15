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

use Cake\View\Helper\FormHelper as BaseFormHelper;
use Cake\View\View;

/**
 * Provides functionalities for forms
 * @property \MeTools\View\Helper\IconHelper $Icon
 * @property \MeTools\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class FormHelper extends BaseFormHelper
{
    use AddButtonClassesTrait;

    /**
     * Helpers
     * @var array
     */
    public $helpers = ['MeTools.Html', 'MeTools.Icon', 'Url'];

    /**
     * @var bool
     */
    protected bool $isInline = false;

    /**
     * @var bool
     */
    protected bool $validation = true;

    /**
     * Construct the widgets and binds the default context providers.
     *
     * This method only rewrites the default config.
     * @param \Cake\View\View $view The View this helper is being attached to
     * @param array<string, mixed> $config Configuration settings for the helper
     */
    public function __construct(View $view, array $config = [])
    {
        /**
         * Rewrites default templates config
         */
        $this->_defaultConfig['templates'] = [
            //Used for button elements in button()
            'button' => '<button{{attrs}}>{{icon}}{{text}}</button>',
            //Error message wrapper elements
            'error' => '<div class="invalid-feedback" id="{{id}}">{{content}}</div>',
            //Container for error items
            'errorList' => '<ul class="ps-3">{{content}}</ul>',
            //Label element when inputs are not nested inside the label
            'label' => '<label{{attrs}}>{{icon}}{{text}}</label>',
            //Container element used by control()
            'inputContainer' => '<div class="{{divClass}}{{type}}{{required}}">{{content}}{{help}}</div>',
            //Container element used by control() when a field has an error
            'inputContainerError' => '<div class="{{divClass}}{{type}}{{required}} error">{{content}}{{error}}{{help}}</div>',
            //Label element used for radio and multi-checkbox inputs
            'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>',
            //Container for submit buttons
            'submitContainer' => '<div class="{{divClass}}submit">{{content}}</div>',
        ] + $this->_defaultConfig['templates'];

        /**
         * This value can be changed (via the `$_config` property) by the `create()`/`end()` methods
         * @see https://getbootstrap.com/docs/5.3/forms/validation/#server-side
         */
        $this->_defaultConfig['errorClass'] = 'is-invalid';

        parent::__construct($view, $config);
    }

    /**
     * Returns the input type that was guessed for the provided fieldName, based on the internal type it is associated
     *  too, its name and the variables that can be found in the view template
     * @param string $fieldName the name of the field to guess a type for
     * @param array<string, mixed> $options the options passed to the input method
     * @return string
     */
    protected function _inputType(string $fieldName, array $options): string
    {
        $type = $options['type'] ?? parent::_inputType($fieldName, $options);

        //Forces the `password` type if the current type is `text` and `$fieldName` contains "password" or "pwd" words
        return $type == 'text' && (str_contains($fieldName, 'password') || str_contains($fieldName, 'pwd')) ? 'password' : $type;
    }

    /**
     * Generate label for input
     * @param string $fieldName The name of the field to generate label for
     * @param array<string, mixed> $options Options list
     * @return string|false Generated label element or false
     */
    protected function _getLabel(string $fieldName, array $options)
    {
        if ($options['label'] === false) {
            return false;
        }

        $label = is_string($options['label']) ? ['text' => $options['label']] : ($options['label'] ?? []);

        /**
         * Sets the label class.
         * Checkboxes and radios always have their own `form-check-label` class, even in inline forms. Other input types,
         *  on inline forms, have the `visually-hidden` class. In all other cases, the default class is `form-label`.
         * @todo what about `floatingInput` forms?
         * @todo what about horizontal forms?
         */
        $type = $this->_inputType($fieldName, $options);
        if (in_array($type, ['checkbox', 'radio'])) {
            $class = 'form-check-label';
        } elseif ((empty($label['text']) && $type == 'ckeditor') || $this->isInline()) {
            $class = 'visually-hidden';
        }
        $label = $this->addClass($label, $class ?? 'form-label');

        if ($label['icon'] ?? false) {
            $options['templateVars']['icon'] = $this->Icon->icon($label['icon'] . ' ');
            unset($label['icon']);
        }

        return parent::_getLabel($fieldName, compact('label') + $options);
    }

    /**
     * Checks if the currently created form is an inline form
     * @return bool
     */
    public function isInline(): bool
    {
        return $this->isInline;
    }

    /**
     * Creates a `<button>` tag
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string A HTML button tag
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-button-elements
     * @see \Cake\View\Helper\FormHelper::button() for all available options
     * @throws \Tools\Exception\NotInArrayException
     */
    public function button(string $title, array $options = []): string
    {
        $options += ['escapeTitle' => false, 'icon' => null, 'templateVars' => [], 'type' => 'button'];
        $options = $this->addButtonClasses($options, 'btn-primary');
        if ($options['icon']) {
            $options['templateVars'] += ['icon' => $this->Icon->icon($options['icon']) . ' '];
            unset($options['icon']);
        }

        return parent::button($title, $options);
    }

    /**
     * Creates a CKEditor textarea
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array<string, mixed> $options Each type of input takes different options
     * @return string
     * @see \MeTools\View\Helper\LibraryHelper::ckeditor() to add the scripts for CKEditor
     */
    public function ckeditor(string $fieldName, array $options = []): string
    {
        $options += ['type' => 'textarea'];
        $options = $this->addClass($options, 'editor wysiwyg');

        return parent::textarea($fieldName, $options);
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * ### Options:
     *
     *  - `append-text` to append a text
     *  - `help` to add a help text
     *  - `prepend-text` to prepend a text
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array<string, mixed> $options Each type of input takes different options
     * @return string Completed form widget
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-form-controls
     * @see \Cake\View\Helper\FormHelper::control() for all available options
     */
    public function control(string $fieldName, array $options = []): string
    {
        $options += ['help' => null, 'append-text' => null, 'prepend-text' => null, 'templateVars' => []];

        $templateVars['divClass'] = 'mb-3 ';
        if ($this->isInline()) {
            $templateVars['divClass'] = 'col-12 ';
        }

        switch ($this->_inputType($fieldName, $options)) {
            case 'checkbox':
            case 'radio':
                $templateVars['divClass'] .= 'form-check ';
                $class = 'form-check-input';
                break;
            case 'select':
                $class = 'form-select';
                break;
        }
        $options = $this->addClass($options, $class ?? 'form-control');

        /**
         * Help text (form text).
         * These are ignored in inline forms.
         * @see https://getbootstrap.com/docs/5.3/forms/form-control/#form-text
         * @todo Form text should be explicitly associated with the form control it relates to using the aria-labelledby
         *  (for mandatory information such as data format) or aria-describedby (for complementary information) attribute
         */
        if ($options['help'] && !$this->isInline()) {
            $help = array_map(fn(string $help): string => $this->Html->div('form-text', trim($help)), (array)$options['help']);
            $templateVars['help'] = implode('', $help);
        }

        /**
         * Input group (`append-text` and `prepend-text` options).
         * It can also handle buttons.
         * @see https://getbootstrap.com/docs/5.3/forms/input-group
         */
        if ($options['append-text'] || $options['prepend-text']) {
            foreach (['append', 'prepend'] as $name) {
                $value = $options[$name . '-text'];
                //Buttons and submits are not wrapped in a `span` element
                if ($value && !str_starts_with($value, '<button') && !str_starts_with($value, '<div class="submit') &&
                    !str_starts_with($value, '<div class="col-12 submit')) {
                    $value = $this->Html->span($value, ['class' => 'input-group-text']);
                }

                $templateVars[$name] = $value;
            }
            $options['templates']['formGroup'] = '{{label}}<div class="input-group">{{prepend}}{{input}}{{append}}{{error}}</div>';
        }

        $options['templateVars'] += $templateVars;

        unset($options['help'], $options['append-text'], $options['prepend-text']);

        return parent::control($fieldName, $options);
    }

    /**
     * Returns an HTML form element.
     *
     * ### Options:
     *
     *  - `validation`, if `false` it disables field validation
     * @param mixed $context The context for which the form is being defined. Can be a ContextInterface instance, ORM
     *  entity, ORM resultset, or an array of meta data. You can use `null` to make a context-less form
     * @param array<string, mixed> $options An array of html attributes and options
     * @return string A formatted opening FORM tag
     * @see \Cake\View\Helper\FormHelper::create() for all available options
     */
    public function create($context = null, array $options = []): string
    {
        if (isset($options['validation'])) {
            $this->validation = $options['validation'];
            unset($options['validation']);
        }
        if (!$this->validation) {
            $this->_config['errorClass'] = '';
        }

        return parent::create($context, $options);
    }

    /**
     * Returns an inline HTML form element
     * @param mixed $context The context for which the form is being defined. Can be a ContextInterface instance, ORM
     *  entity, ORM resultset, or an array of meta data. You can use `null` to make a context-less form
     * @param array<string, mixed> $options An array of html attributes and options
     * @return string A formatted opening FORM tag
     * @see \MeTools\View\Helper\FormHelper::create()
     * @see \Cake\View\Helper\FormHelper::create()  for all available options
     */
    public function createInline($context = null, array $options = []): string
    {
        $this->isInline = true;

        $options = $this->addClass($options, 'row row-cols-lg-auto g-1 align-items-center');

        return $this->create($context, $options);
    }

    /**
     * Closes an HTML form, cleans up values set by FormHelper::create(), and writes hidden input fields where appropriate
     * @param array<string, mixed> $secureAttributes Secure attributes which will be passed as HTML attributes into the
     *  hidden input elements generated for the Security Component
     * @return string A closing FORM tag
     */
    public function end(array $secureAttributes = []): string
    {
        $this->_config['errorClass'] = $this->_defaultConfig['errorClass'];
        $this->isInline = false;
        $this->validation = true;

        return parent::end($secureAttributes);
    }

    /**
     * Create a `<button>` tag with a surrounding `<form>` that submits via POST as default
     * @param string $title The button's caption. Not automatically HTML encoded
     * @param array|string $url URL as string or array
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string A HTML button tag
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-standalone-buttons-and-post-links
     * @see \Cake\View\Helper\FormHelper::postButton() for all available options
     */
    public function postButton(string $title, $url, array $options = []): string
    {
        return parent::postButton($title, $url, ['type' => 'submit'] + $options);
    }

    /**
     * Returns a formatted SELECT element
     * @param string $fieldName Name attribute of the SELECT
     * @param iterable $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the SELECT element
     * @param array<string, mixed> $attributes The HTML attributes of the select element
     * @return string Formatted SELECT element
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-select-pickers
     * @see \Cake\View\Helper\FormHelper::select() for all available options
     */
    public function select(string $fieldName, iterable $options = [], array $attributes = []): string
    {
        $attributes += ['multiple' => null, 'required' => null, 'empty' => null, 'default' => null];

        /**
         * The `empty` attribute is added only if:
         *  - the `empty` and `default` attributes are empty;
         *  - it is not a multiple.
         * @todo what about `value` option?
         */
        if ($attributes['empty'] == null && !$attributes['default'] && !$attributes['multiple']) {
            //If the field is marked as `required`, an `empty` text will be added
            if ($this->_getContext()->isRequired($fieldName) || $attributes['required']) {
                $empty = '-- ' . __d('me-tools', 'select a value') . ' --';
            }
            $attributes['empty'] = $empty ?? true;
        }

        return parent::select($fieldName, $options, $attributes);
    }

    /**
     * Creates a submit button element
     * @param string|null $caption The label appearing on the button OR if string contains :// or the extension .jpg,
     *  .jpe, .jpeg, .gif, .png use an image if the extension exists, AND the first character is /, image is relative to
     *  webroot, OR if the first character is not /, image is relative to webroot/img
     * @param array<string, mixed> $options Array of option
     * @return string A HTML submit button
     * @link https://book.cakephp.org/4/en/views/helpers/form.html#creating-buttons-and-submit-elements
     * @see \Cake\View\Helper\FormHelper::submit() for all available options
     * @throws \Tools\Exception\NotInArrayException
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        $options += ['templateVars' => []];

        if ($this->isInline()) {
            $options['templateVars'] += ['divClass' => 'col-12 '];
        }

        $options = $this->addButtonClasses($options, 'btn-primary');

        return parent::submit($caption, $options);
    }
}
