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
     * @inheritDoc
     */
    public function __construct(View $view, array $config = [])
    {
        /**
         * Rewrites default templates config
         * @see \Cake\View\Helper\FormHelper::$_defaultConfig
         */
        $this->_defaultConfig['templates'] = [
            //Used for button elements in button()
            'button' => '<button{{attrs}}>{{icon}}{{text}}</button>',
            //Wrapper container for checkboxes
            'checkboxWrapper' => '<div class="form-check">{{label}}</div>',
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
        ] + [
            /**
             * @todo these should be deleted with CakePHP 4.5
             */
            //Generic input element
            'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            //Submit input element
            'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
        ] + $this->_defaultConfig['templates'];

        /**
         * This value can be changed (via the `$_config` property) by the `create()`/`end()` methods
         * @see https://getbootstrap.com/docs/5.3/forms/validation/#server-side
         */
        $this->_defaultConfig['errorClass'] = 'is-invalid';

        parent::__construct($view, $config);
    }

    /**
     * @inheritDoc
     */
    protected function _inputType(string $fieldName, array $options): string
    {
        $type = $options['type'] ?? parent::_inputType($fieldName, $options);

        //Forces the `password` type if the current type is `text` and `$fieldName` contains "password" or "pwd" words
        return $type == 'text' && (str_contains($fieldName, 'password') || str_contains($fieldName, 'pwd')) ? 'password' : $type;
    }

    /**
     * @inheritDoc
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
            $class = 'form-label form-check-label';
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
     * @inheritDoc
     */
    public function button(string $title, array $options = []): string
    {
        $options += ['escapeTitle' => false, 'icon' => null, 'templateVars' => [], 'type' => 'button'];
        if ($options['icon']) {
            $options['templateVars'] += ['icon' => $this->Icon->icon($options['icon']) . ' '];
            unset($options['icon']);
        }

        return parent::button($title, $this->addButtonClasses($options));
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
        $options += ['escape' => false, 'help' => null, 'append-text' => null, 'prepend-text' => null, 'templates' => [], 'templateVars' => []];

        $templateVars['divClass'] = 'mb-3 ';
        if ($this->isInline()) {
            $templateVars['divClass'] = 'col-12 ';
        }

        switch ($this->_inputType($fieldName, $options)) {
            case 'checkbox':
                $templateVars['divClass'] .= 'form-check ';
                $class = 'form-check-input';
                break;
            case 'radio':
                $options['templates'] += ['radioWrapper' => '<div class="form-check">{{label}}</div>'];
                $class = 'form-check-input';
                break;
            case 'select':
                $class = 'form-select';
                break;
            case 'time':
                $options += ['step' => 60];
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
            $options['templates'] += [
                'formGroup' => '{{label}}<div class="input-group">{{prepend}}{{input}}{{append}}{{error}}</div>',
                'inputContainerError' => '<div class="{{divClass}}{{type}}{{required}} error">{{content}}{{help}}</div>',
            ];
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

        $options = $this->addClass($options, 'row row-cols-lg-auto g-3 align-items-center');

        return $this->create($context, $options);
    }

    /**
     * @inheritDoc
     */
    public function end(array $secureAttributes = []): string
    {
        $this->_config['errorClass'] = $this->_defaultConfig['errorClass'];
        $this->isInline = false;
        $this->validation = true;

        return parent::end($secureAttributes);
    }

    /**
     * @inheritDoc
     */
    public function postButton(string $title, $url, array $options = []): string
    {
        return parent::postButton($title, $url, ['type' => 'submit'] + $this->addButtonClasses($options));
    }

    /**
     * @inheritDoc
     */
    public function select(string $fieldName, iterable $options = [], array $attributes = []): string
    {
        $attributes += ['multiple' => null, 'required' => null, 'empty' => null, 'default' => null, 'label' => null];

        /**
         * The `empty` attribute is added only if:
         *  - the `empty` attribute is empty;
         *  - the `default` attributes is empty;
         *  - it is not a multiple.
         * @todo what about `value` option?
         */
        if ($attributes['empty'] == null && !$attributes['default'] && !($attributes['multiple'] || in_array('multiple', $attributes, true))) {
            //If the field is marked as `required`, an `empty` text will be added
            if ($this->_getContext()->isRequired($fieldName) || $attributes['required']) {
                $empty = '-- ' . __d('me_tools', 'select a value') . ' --';
            }
            $attributes['empty'] = $empty ?? true;
        }

        //Sets input class and label class for multiple checkboxes
        if ($attributes['multiple'] === 'checkbox') {
            $attributes['class'] = 'form-check-input';
            $attributes['label'] = $attributes['label'] === true ? ['class' => 'form-check-label'] : $attributes['label'];
        }

        return parent::select($fieldName, $options, $attributes);
    }

    /**
     * @inheritDoc
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        $options += ['templateVars' => []];

        if ($this->isInline()) {
            $options['templateVars'] += ['divClass' => 'col-12 '];
        }

        return parent::submit($caption, $this->addButtonClasses($options));
    }
}
