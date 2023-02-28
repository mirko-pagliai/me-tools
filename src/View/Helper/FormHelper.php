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
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'MeTools.Html',
        'MeTools.Icon',
        'Url',
    ];

    /**
     * @var bool
     */
    protected bool $isInline = false;

    /**
     * @var bool
     */
    protected bool $isPost;

    /**
     * @var bool
     */
    protected bool $validation = true;

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
            //Container element user for checkboxes when has an error
            'checkboxContainerError' => '<div class="input mb-3 form-check{{required}}">{{content}}{{error}}{{help}}</div>',
            //Error message wrapper elements
            'error' => '<div class="invalid-feedback" id="{{id}}">{{content}}</div>',
            //Container element used by `control()`
            'inputContainer' => '<div class="input mb-3 {{type}}{{required}}">{{content}}{{help}}</div>',
            //Container element used by `control()` when a field has an error
            'inputContainerError' => '<div class="input mb-3 {{type}}{{required}} error">{{content}}{{error}}{{help}}</div>',
            // Submit/reset button
            'inputSubmit' => '<button{{attrs}}>{{text}}</button>',
        ]]);

        parent::__construct($view, $config);

        $this->isPost = $this->getView()->getRequest()->is('post');
    }

    /**
     * Generates an input element
     * @param string $fieldName the field name
     * @param array<string, mixed> $options The options for the input element
     * @return array|string The generated input element string or array if checkbox() is called with option 'hiddenField'
     *  set to '_split'
     */
    protected function _getInput(string $fieldName, array $options)
    {
        $options = optionsParser($options);

        //Class (checkboxes and radios have their own class)
        if (!in_array($options->get('type'), ['checkbox', 'radio'])) {
            $options->append('class', 'form-control');
        }

        /**
         * Add class on `post` request and if validation is on (the form has been filled out)
         * @see https://getbootstrap.com/docs/5.2/forms/validation/#server-side
         */
        if ($this->isPost && $this->validation) {
            $options->append('class', $this->isFieldError($fieldName) ? 'is-invalid' : 'is-valid');
        }

        return parent::_getInput($fieldName, $options->toArray());
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

        $label = optionsParser(is_string($options['label']) ? ['text' => $options['label']] : ($options['label'] ?? []));
        $label->append('class', $options['type'] === 'checkbox' ? 'form-check-label' : ($this->isInline() ? 'visually-hidden' : 'form-label'));

        return parent::_getLabel($fieldName, ['label' => $label->toArray()] + $options);
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
        //Forces the `password` type if `$fieldName` contains "password" or "pwd" words
        if (str_contains($fieldName, 'password') || str_contains($fieldName, 'pwd')) {
            return 'password';
        }

        return parent::_inputType($fieldName, $options);
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
     * Creates a CKEditor textarea.
     *
     * To add the scripts for CKEditor, you should use the `LibraryHelper`.
     * @param string $fieldName This should be "modelname.fieldname"
     * @param array<string, mixed> $options Each type of input takes different options
     * @return string
     * @see \MeTools\View\Helper\LibraryHelper::ckeditor()
     */
    public function ckeditor(string $fieldName, array $options = []): string
    {
        $options = optionsParser($options, ['label' => false, 'type' => 'textarea']);
        $options->append('class', 'wysiwyg editor');

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Creates a checkbox input widget.
     *
     * See the parent method for all available options.
     * @param string $fieldName Name of a field, like this "modelname.fieldname"
     * @param array<string, mixed> $options Array of HTML attributes
     * @return array<string>|string An HTML text input element
     */
    public function checkbox(string $fieldName, array $options = [])
    {
        $options = optionsParser($options)->append('class', 'form-check-input');

        if ($this->isInline()) {
            $this->setTemplates([
                'checkboxContainer' => '<div class="col-12"><div class="form-check{{required}}">{{content}}</div></div>',
                'checkboxContainerError' => '<div class="col-12"><div class="form-check{{required}} error">{{content}}{{error}}</div></div>',
            ]);
        }

        return parent::checkbox($fieldName, $options->toArray());
    }

    /**
     * Generates a form control element complete with label and wrapper div.
     *
     * ### Options:
     *
     *  - `append-text` to append a text
     *  - `help` to add a help text
     *  - `prepend-text` to prepend a text
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

        /**
         * Inline forms.
         * By default, no help blocks.
         * @see https://getbootstrap.com/docs/5.2/forms/layout/#inline-forms
         */
        if ($this->isInline()) {
            $this->setTemplates([
                'inputContainer' => '<div class="col-12 {{type}}{{required}}">{{content}}</div>',
                'inputContainerError' => '<div class="col-12 {{type}}{{required}} error">{{content}{{error}}</div>',
            ]);
        }

        if ($options->get('type') === 'radio') {
            $this->setTemplates(['nestingLabel' => '<div class="form-check">{{hidden}}{{input}}<label{{attrs}}>{{text}}</label></div>']);
        }

        /**
         * Help text (form text).
         * These are ignored in inline forms.
         * @see https://getbootstrap.com/docs/5.2/forms/overview/#form-text
         */
        if ($options->exists('help') && !$this->isInline()) {
            $help = implode('', array_map(fn(string $help): string => $this->Html->div('form-text text-muted', trim($help)), (array)$options->consume('help')));
            $options->append('templateVars', compact('help'));
        }

        /**
         * Input group (`append-text` and `prepend-text` options).
         * It can also handle buttons.
         * @see https://getbootstrap.com/docs/5.2/forms/input-group
         */
        if ($options->exists('append-text') || $options->exists('prepend-text')) {
            $validationClass = $this->isPost && $this->validation ? ' has-validation' : '';
            $this->setTemplates([
                'formGroup' => '{{label}}<div class="input-group' . $validationClass . '">{{prepend}}{{input}}{{append}}{{error}}</div>',
                'inputContainer' => '<div class="input mb-3 {{type}}{{required}}">{{content}}{{help}}</div>',
                'inputContainerError' => '<div class="input mb-3 {{type}}{{required}} error">{{content}}{{help}}</div>',
            ]);

            foreach (['append', 'prepend'] as $name) {
                $value = $options->consume($name . '-text') ?: '';
                if ($value && !str_starts_with($value, '<button') && !str_starts_with($value, '<div class="submit') && !str_starts_with($value, '<div class="col-12 submit')) {
                    $value = $this->Html->span($value, ['class' => 'input-group-text']);
                }
                $templateVars[$name] = $value;
            }
            $options->append('templateVars', $templateVars);
        }

        return parent::control($fieldName, $options->toArray());
    }

    /**
     * Returns an HTML form element.
     *
     * ### Options:
     *
     *  - `validation`, if `false` it disables field validation
     *
     * See the parent method for all available options.
     * @param mixed $context The context for which the form is being defined. Can be a ContextInterface instance, ORM
     *  entity, ORM resultset, or an array of meta data. You can use `null` to make a context-less form
     * @param array<string, mixed> $options An array of html attributes and options
     * @return string A formatted opening FORM tag
     */
    public function create($context = null, array $options = []): string
    {
        if (isset($options['validation'])) {
            $this->validation = $options['validation'];
            unset($options['validation']);
        }

        return parent::create($context, $options);
    }

    /**
     * Returns an inline HTML form element.
     *
     * See the parent method for all available options.
     * @param mixed $context The context for which the form is being defined. Can be a ContextInterface instance, ORM
     *  entity, ORM resultset, or an array of metadata. You can use `null` to make a context-less form.
     * @param array<string, mixed> $options An array of html attributes and options
     * @return string A formatted opening FORM tag
     * @see https://getbootstrap.com/docs/5.2/forms/layout/#inline-forms
     */
    public function createInline($context = null, array $options = []): string
    {
        $this->isInline = true;
        $options = optionsParser($options)->append('class', 'row row-cols-lg-auto g-1 align-items-center');

        return parent::create($context, $options->toArray());
    }

    /**
     * Closes an HTML form.
     *
     * See the parent method for all available options.
     * @param array<string, mixed> $secureAttributes Secure attributes
     * @return string A closing FORM tag
     */
    public function end(array $secureAttributes = []): string
    {
        $this->isInline = false;
        $this->validation = true;

        return parent::end($secureAttributes);
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
     * Returns a formatted LABEL element for HTML forms.
     *
     * Will automatically generate a `for` attribute if one is not provided.
     *
     * See the parent method for all available options.
     * @param string $fieldName This should be "modelname.fieldname"
     * @param string|null $text Text that will appear in the label field. If $text is left undefined the text will be
     *  inflected from the fieldName
     * @param array<string, mixed> $options An array of HTML attributes
     * @return string The formatted LABEL element
     */
    public function label(string $fieldName, ?string $text = null, array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false]);

        if (!$this->isInline()) {
            $options->append('class', 'fw-bolder');
            [$text, $options] = $this->Icon->addIconToText($text, $options);
        }

        return parent::label($fieldName, $text, $options->toArray());
    }

    /**
     * Creates a "POST button", which is a "POST link" with all the appearance of a button
     * @param string $title The content to be wrapped by <a> tags
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param array<string, mixed> $options Array of HTML attributes
     * @return string An `<a />` element
     */
    public function postButton(string $title = '', $url = null, array $options = []): string
    {
        $options = optionsParser($options)->add('role', 'button')->addButtonClasses();

        return $this->postLink($title, $url, $options->toArray());
    }

    /**
     * Creates a set of radio widgets.
     *
     * See the parent method for all available options and attributes.
     * @param string $fieldName Name of a field, like this "modelname.fieldname"
     * @param iterable $options Radio button options array
     * @param array<string, mixed> $attributes Array of attributes
     * @return string Completed radio widget set
     */
    public function radio(string $fieldName, iterable $options = [], array $attributes = []): string
    {
        $attributes = optionsParser($attributes)
            ->append('class', 'form-check-input')
            ->add('label', ['class' => 'form-check-label']);

        //Sets the `nestingLabel` templates only if it is still the default one, therefore not already modified by other methods
        if ($this->getTemplates('nestingLabel') == $this->_defaultConfig['templates']['nestingLabel']) {
            $this->setTemplates(['nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label>']);
        }

        $this->setTemplates(['label' => '']);

        return parent::radio($fieldName, $options, $attributes->toArray());
    }

    /**
     * Returns a formatted SELECT element.
     *
     * See the parent method for all available options and attributes.
     * @param string $fieldName Name attribute of the SELECT
     * @param iterable $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the SELECT element
     * @param array<string, mixed> $attributes The HTML attributes of the select element.
     * @return string Formatted SELECT element
     */
    public function select(string $fieldName, iterable $options = [], array $attributes = []): string
    {
        $attributes = optionsParser($attributes);
        if (!$attributes->exists('default') && !$attributes->exists('value')) {
            $attributes->addDefault('empty', true);
        }
        $attributes->append('class', 'form-select');

        return parent::select($fieldName, $options, $attributes->toArray());
    }

    /**
     * Creates a submit button element. This method will generate `<input />` elements that can be used to submit, and
     *  reset forms by using $options. Image submits can be created by supplying an image path for $caption.
     *
     * See the parent method for all available options.
     * @param string|null $caption The label appearing on the button OR if string contains :// or the extension .jpg,
     *  .jpe, .jpeg, .gif, .png use an image if the extension exists, AND the first character is /, image is relative to
     *  webroot, OR if the first character is not /, image is relative to webroot/img
     * @param array<string, mixed> $options Array of options
     * @return string An HTML submit button
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false, 'type' => 'submit']);

        $options->addButtonClasses($options->contains('type', 'submit') ? 'success' : 'primary');
        [$text, $options] = $this->Icon->addIconToText($caption, $options);
        $options->append('templateVars', ['text' => $text ?? __d('cake', 'Submit')]);

        if ($this->isInline()) {
            $this->setTemplates(['submitContainer' => '<div class="col-12 submit">{{content}}</div>']);
        }

        return parent::submit($caption, $options->toArray());
    }
}
