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
use Cake\View\Helper\FormHelper as CakeFormHelper;
use Cake\View\View;
use MeTools\View\OptionsParser;
use MeTools\View\Widget\HiddenWidget;

/**
 * Provides functionalities for forms
 * @property \MeTools\View\Helper\IconHelper $Icon
 */
class FormHelper extends CakeFormHelper
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
     * Property to check if we're working with an inline form.
     * It's changed by `createInline()` method.
     * @var bool
     */
    protected $inline = false;

    /**
     * Construct the widgets and binds the default context providers.
     *
     * This method only rewrites the default templates config.
     * @param \Cake\View\View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     * @return void
     */
    public function __construct(View $view, array $config = [])
    {
        //Rewrites default templates config
        $this->_defaultConfig = Hash::merge($this->_defaultConfig, ['templates' => [
            'checkboxContainer' => '<div class="form-check input {{type}}{{required}}">{{content}}</div>',
            'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}} {{text}}{{help}}</label>',
            'hidden' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            'input' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
            'inputError' => '<input type="{{type}}" name="{{name}}" class="form-control is-invalid"{{attrs}}/>',
            'inputContainer' => '<div class="form-group input {{type}}{{required}}">{{content}}{{help}}</div>',
            'inputContainerError' => '<div class="form-group input {{type}}{{required}} error">{{content}}{{error}}</div>',
            'select' => '<select name="{{name}}" class="form-control"{{attrs}}>{{content}}</select>',
            'selectMultiple' => '<select name="{{name}}[]" multiple="multiple" class="form-control"{{attrs}}>{{content}}</select>',
            'textarea' => '<textarea name="{{name}}" class="form-control"{{attrs}}>{{value}}</textarea>',
        ]]);
        $this->_defaultWidgets['hidden'] = [HiddenWidget::class];

        parent::__construct($view, $config);
    }

    /**
     * Internal method to get an `OptionParser` instance for datetime pickers
     * @param array $options HTML attributes and options
     * @param string $class Class name
     * @param string $dateFormat Date time format
     * @return \MeTools\View\OptionsParser
     * @since 2.18.12
     */
    protected function __datetimepickerOptions(array $options, string $class, string $dateFormat): OptionsParser
    {
        return optionsParser($options, ['data-date-format' => $dateFormat, 'type' => 'text'])
            ->append('templates', [
                'input' => '<input type="{{type}}" name="{{name}}" class="form-control ' . $class . '"{{attrs}}/>',
                'inputError' => '<input type="{{type}}" name="{{name}}" class="form-control ' . $class . ' is-invalid"{{attrs}}/>',
            ]);
    }

    /**
     * Creates a button.
     *
     * This method creates a button. To create a POST button, you should use
     *  the `postButton()` method.
     * Instead, to create a link with the appearance of a button, you should
     *  use the `button()` method provided by `HtmlHelper`.
     * @param string $title The button label or an image
     * @param array $options HTML attributes and options
     * @return string
     * @see postButton()
     * @see MeTools\View\Helper\HtmlHelper::button()
     */
    public function button(string $title = '', array $options = []): string
    {
        $options = optionsParser($options, ['escapeTitle' => false, 'type' => 'button']);
        $options->addButtonClasses($options->contains('type', 'submit') ? 'success' : 'primary');
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::button($title, $options->toArray());
    }

    /**
     * Creates a checkbox element
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string|array<string> An HTML text input element
     */
    public function checkbox(string $fieldName, array $options = [])
    {
        $options = optionsParser($options);

        if (!$options->exists('hiddenField') || !$options->contains('hiddenField', false)) {
            $options->add('hiddenField', true);
        }

        return parent::checkbox($fieldName, $options->toArray());
    }

    /**
     * Creates a CKEditor textarea.
     *
     * To add the scripts for CKEditor, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::ckeditor()
     */
    public function ckeditor(string $fieldName, array $options = []): string
    {
        $options = optionsParser($options, ['label' => false, 'type' => 'textarea'])
            ->append('templates', [
                'textarea' => '<textarea name="{{name}}" class="form-control wysiwyg editor"{{attrs}}>{{value}}</textarea>',
            ]);

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Generates an input element complete with label and wrapper div
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     */
    public function control(string $fieldName, array $options = []): string
    {
        $this->resetTemplates();

        $options = optionsParser($options);

        //If the name contains the "password" word, then the type is `password`
        if (string_contains($fieldName, 'password')) {
            $options->Default->add(['type' => 'password']);
        }

        //Gets the input type
        $type = $options->get('type') ?: $this->_inputType($fieldName, $options->toArray());
        if ($type === 'select' && !$options->exists('default') && !$options->exists('value')) {
            $options->Default->add(['empty' => true]);
        }

        //Help text
        //See https://getbootstrap.com/docs/4.0/components/forms/#help-text
        if ($options->exists('help')) {
            $help = array_map(function (string $help): string {
                return $this->Html->para('form-text text-muted', trim($help));
            }, (array)$options->consume('help'));
            $options->append('templateVars', ['help' => implode('', $help)]);
        }

        //Input group. Fixes templates
        //See https://getbootstrap.com/docs/4.0/components/input-group/
        if ($options->exists('button')) {
            $options->append([
                'templates' => ['formGroup' => '{{label}}<div class="input-group">{{input}}{{button}}</div>'],
                'templateVars' => ['button' => $this->Html->div('input-group-append', $options->consume('button'))],
            ]);
        }

        //If is an inline form
        if ($this->inline) {
            //By default, no help blocks
            $options->append('templates', [
                'inputContainer' => '<div class="form-group input {{type}}{{required}}">{{content}}</div>',
            ]);

            //If it is not a checkbox
            if ($type !== 'checkbox' && (!$options->exists('label') || $options->get('label') !== false)) {
                $label = $options->get('label') ? ['text' => $options->get('label')] : [];
                $label = optionsParser($label)->append('class', 'sr-only');
                $options->add('label', $label->toArray());
            }
        }

        return parent::control($fieldName, $options->toArray());
    }

    /**
     * Returns a `<form>` element.
     * @param mixed $context The context for which the form is being defined.
     *   Can be a ContextInterface instance, ORM entity, ORM resultset, or an
     *   array of meta data. You can use `null` to make a context-less form
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
     */
    public function create($context = null, array $options = []): string
    {
        $options = optionsParser($options);

        //It's a form inline with the `inline` option or the `form-inline` class
        if ($options->exists('inline') || $options->contains('class', 'form-inline')) {
            return $this->createInline($context, $options->toArray());
        }

        return parent::create($context, $options->toArray());
    }

    /**
     * Returns an inline form element.
     *
     * You can also create an inline form using the `create()` method with
     *  the `inline` option.
     *
     * Note that by default `createInline` doesn't display help blocks and
     *  errors.
     * @param mixed $context The context for which the form is being defined.
     *   Can be a ContextInterface instance, ORM entity, ORM resultset, or an
     *   array of meta data. You can use `null` to make a context-less form
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
     */
    public function createInline($context = null, array $options = []): string
    {
        $this->inline = true;
        $options = optionsParser($options)->delete('inline')->append('class', 'form-inline');

        return parent::create($context, $options->toArray());
    }

    /**
     * Creates a datepicker.
     *
     * To add the scripts for datepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see \MeTools\View\Helper\LibraryHelper::datepicker()
     */
    public function datepicker(string $fieldName, array $options = []): string
    {
        $options = $this->__datetimepickerOptions($options, 'datepicker', 'YYYY-MM-DD');

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Creates a datetimepicker.
     *
     * To add the scripts for datetimepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see \MeTools\View\Helper\LibraryHelper::datetimepicker()
     */
    public function datetimepicker(string $fieldName, array $options = []): string
    {
        $options = $this->__datetimepickerOptions($options, 'datetimepicker', 'YYYY-MM-DD HH:mm');

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Closes an HTML form, cleans up values set by `FormHelper::create()`,
     *  and writes hidden input fields where appropriate
     * @param array $secureAttributes Secure attibutes which will be passed
     *  as HTML attributes into the hidden input elements generated for the
     *  Security Component.
     * @return string
     */
    public function end(array $secureAttributes = []): string
    {
        $this->inline = false;

        return parent::end($secureAttributes);
    }

    /**
     * Checks if the current opened form is an inline form
     * @return bool
     */
    public function isInline(): bool
    {
        return !empty($this->inline);
    }

    /**
     * Returns a formatted `<label>` element.
     * Will automatically generate a `for` attribute if one is not provided.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param string|null $text Text that will appear in the label field. If is
     *  left undefined the text will be inflected from the fieldName
     * @param array $options HTML attributes
     * @return string
     */
    public function label(string $fieldName, ?string $text = null, array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false]);
        [$text, $options] = $this->Icon->addIconToText($text, $options);

        return parent::label($fieldName, $text, $options->toArray());
    }

    /**
     * Creates a button with a surrounding form that submits via POST.
     *
     * This method creates a button in a form element. So don't use this
     *  method in an already opened form.
     *
     * To create a normal button, you should use the `button()` method.
     * To create a button with the appearance of a link, you should use the
     *  `button()` method provided by the `HtmlHelper`.
     * @param string $title Button title
     * @param string|array|null $url Cake-relative URL or array of URL
     *  parameters or external URL
     * @param array $options Array of options and HTML attributes
     * @return string Html code
     */
    public function postButton(string $title = '', $url = null, array $options = []): string
    {
        $options = optionsParser($options)->add('role', 'button')->addButtonClasses();

        return $this->postLink($title, $url, $options->toArray());
    }

    /**
     * Creates a link with a surrounding form that submits via POST.
     *
     * This method creates a link in a form element. So don't use this method
     *  in an already opened form.
     *
     * To create a normal link, you should use the `link()` method of the
     *  `HtmlHelper`.
     * @param string $title The content to be wrapped by <a> tags
     * @param string|array|null $url Cake-relative URL or array of URL
     *  parameters or external URL
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function postLink(string $title = '', $url = null, array $options = []): string
    {
        $options = optionsParser($options, ['escape' => false, 'title' => $title]);
        $options->add('title', trim(h(strip_tags($options->get('title') ?? ''))))->tooltip();
        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::postLink($title ?? '', $url, $options->toArray());
    }

    /**
     * Returns a formatted SELECT element
     * @param string $fieldName Name attribute of the SELECT
     * @param iterable $options Array of the OPTION elements
     *  (as 'value'=>'Text' pairs) to be used in the SELECT element
     * @param array $attributes The HTML attributes of the select element
     * @return string
     */
    public function select(string $fieldName, iterable $options = [], array $attributes = []): string
    {
        $attributes = optionsParser($attributes);

        if (!$attributes->exists('default') && !$attributes->exists('value')) {
            $attributes->Default->add('empty', true);
        }

        return parent::select($fieldName, $options, $attributes->toArray());
    }

    /**
     * Creates a submit button
     * @param string|null $caption The label appearing on the submit button or
     *  an image
     * @param array $options HTML attributes and options
     * @return string
     */
    public function submit(?string $caption = null, array $options = []): string
    {
        return $this->button($caption ?: '', optionsParser($options)->add('type', 'submit')->toArray());
    }

    /**
     * Creates a textarea widget
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     */
    public function textarea(string $fieldName, array $options = []): string
    {
        $options = optionsParser($options, ['cols' => null, 'rows' => null]);

        return parent::textarea($fieldName, $options->toArray());
    }

    /**
     * Creates a timepicker.
     *
     * To add the scripts for timepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see \MeTools\View\Helper\LibraryHelper::timepicker()
     */
    public function timepicker(string $fieldName, array $options = []): string
    {
        $options = $this->__datetimepickerOptions($options, 'timepicker', 'HH:mm');

        return $this->control($fieldName, $options->toArray());
    }
}
