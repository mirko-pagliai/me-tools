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
 * @see         http://api.cakephp.org/3.4/class-Cake.View.Helper.FormHelper.html FormHelper
 */
namespace MeTools\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper\FormHelper as CakeFormHelper;
use Cake\View\View;
use MeTools\View\OptionsParser;

/**
 * Provides functionalities for forms
 */
class FormHelper extends CakeFormHelper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => ME_TOOLS . '.Html'],
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
     * @param Cake\View\View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     * @return void
     * @uses $_defaultConfig
     */
    public function __construct(View $view, $config = [])
    {
        //Rewrites default templates config
        $this->_defaultConfig = Hash::merge($this->_defaultConfig, ['templates' => [
            'checkboxContainer' => '<div class="form-check input {{type}}{{required}}">{{content}}</div>',
            'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}} {{text}}{{help}}</label>',
            'hidden' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            'input' => '<input type="{{type}}" name="{{name}}" class="form-control"{{attrs}}/>',
            'inputError' => '<input type="{{type}}" name="{{name}}" class="form-control is-invalid"{{attrs}}/>',
            'inputContainer' => '<div class="form-group input {{type}}{{required}}">{{content}}{{help}}</div>',
            'select' => '<select name="{{name}}" class="form-control"{{attrs}}>{{content}}</select>',
            'selectMultiple' => '<select name="{{name}}[]" multiple="multiple" class="form-control"{{attrs}}>{{content}}</select>',
            'textarea' => '<textarea name="{{name}}" class="form-control"{{attrs}}>{{value}}</textarea>',
        ]]);
        $this->_defaultWidgets['hidden'] = ['MeTools\View\Widget\HiddenWidget'];

        parent::__construct($view, $config);
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
     * @see postButton(), MeTools\View\Helper\HtmlHelper::button()
     */
    public function button($title, array $options = [])
    {
        $options = new OptionsParser($options, ['type' => 'button']);
        $options->addButtonClasses($options->contains('type', 'submit') ? 'success' : 'primary');
        list($title, $options) = $this->Html->addIconToText($title, $options);

        return parent::button($title, $options->toArray());
    }

    /**
     * Creates a checkbox element
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     */
    public function checkbox($fieldName, array $options = [])
    {
        $options = new OptionsParser($options);

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
     * @uses control()
     */
    public function ckeditor($fieldName, array $options = [])
    {
        $options = new OptionsParser($options, ['label' => false, 'type' => 'textarea']);

        $options->append('templates', [
            'textarea' => '<textarea name="{{name}}" class="form-control wysiwyg editor"{{attrs}}>{{value}}</textarea>',
        ]);

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Generates an input element complete with label and wrapper div
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @uses $inline
     */
    public function control($fieldName, array $options = [])
    {
        //Resets templates
        $this->resetTemplates();

        $options = new OptionsParser($options);

        //If the name contains the "password" word, then the type is `password`
        if (preg_match('/password/', $fieldName)) {
            $options->Default->add(['type' => 'password']);
        }

        //Gets the input type
        $type = $options->get('type') ?: self::_inputType($fieldName, $options->toArray());

        if ($type === 'select' && !$options->exists('default') && !$options->exists('value')) {
            $options->Default->add(['empty' => true]);
        }

        //Help text
        //See https://getbootstrap.com/docs/4.0/components/forms/#help-text
        if ($options->exists('help')) {
            $help = array_map(function ($help) {
                return $this->Html->para('form-text text-muted', trim($help));
            }, (array)$options->get('help'));
            $options->append('templateVars', ['help' => implode(null, $help)]);
            $options->delete('help');
        }

        //Input group
        //See https://getbootstrap.com/docs/4.0/components/input-group/
        if ($options->exists('button')) {
            //Fixes templates
            $options->append([
                'templates' => ['formGroup' => '{{label}}<div class="input-group">{{input}}{{button}}</div>'],
                'templateVars' => ['button' => $this->Html->span($options->get('button'), ['class' => 'input-group-btn'])],
            ]);
            $options->delete('button');
        }

        //If is an inline form
        if ($this->inline) {
            //By default, no help blocks
            $options->append('templates', [
                'inputContainer' => '<div class="form-group input {{type}}{{required}}">{{content}}</div>',
            ]);

            //If it is not a checkbox
            if ($type !== "checkbox" && (!$options->exists('label') || $options->get('label') !== false)) {
                $label = $options->get('label');

                if (!$label) {
                    $label = [];
                } elseif (is_string($label)) {
                    $label = ['text' => $label];
                }

                $label = new OptionsParser($label);
                $label->append('class', 'sr-only');

                $options->add('label', $label->toArray());
            }
        }

        return parent::control($fieldName, $options->toArray());
    }

    /**
     * Returns a `<form>` element.
     * @param mixed $model The model name for which the form is being defined.
     *  If `false` no model is used
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
     * @uses createInline()
     */
    public function create($model = null, array $options = [])
    {
        $options = new OptionsParser($options);

        //It's a form inline with the `inline` option or the `form-inline` class
        if ($options->exists('inline') || $options->contains('class', 'form-inline')) {
            return self::createInline($model, $options->toArray());
        }

        return parent::create($model, $options->toArray());
    }

    /**
     * Returns an inline form element.
     *
     * You can also create an inline form using the `create()` method with
     *  the `inline` option.
     *
     * Note that by default `createInline` doesn't display help blocks and
     *  errors.
     * @param mixed $model The model name for which the form is being defined.
     *  If `false` no model is used
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
     * @uses $inline
     */
    public function createInline($model = null, array $options = [])
    {
        $this->inline = true;

        $options = new OptionsParser($options);
        $options->delete('inline');
        $options->append('class', 'form-inline');

        return parent::create($model, $options->toArray());
    }

    /**
     * Creates a datepicker.
     *
     * To add the scripts for datepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::datepicker()
     * @uses control()
     */
    public function datepicker($fieldName, array $options = [])
    {
        $options = new OptionsParser($options, ['data-date-format' => 'YYYY-MM-DD', 'type' => 'text']);
        $options->append('templates', [
            'input' => '<input type="{{type}}" name="{{name}}" class="form-control datepicker"{{attrs}}/>',
            'inputError' => '<input type="{{type}}" name="{{name}}" class="form-control datepicker is-invalid"{{attrs}}/',
        ]);

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Creates a datetimepicker.
     *
     * To add the scripts for datetimepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::datetimepicker()
     */
    public function datetimepicker($fieldName, array $options = [])
    {
        $options = new OptionsParser($options, ['data-date-format' => 'YYYY-MM-DD HH:mm', 'type' => 'text']);
        $options->append('templates', [
            'input' => '<input type="{{type}}" name="{{name}}" class="form-control datetimepicker"{{attrs}}/>',
            'inputError' => '<input type="{{type}}" name="{{name}}" class="form-control datetimepicker is-invalid"{{attrs}}/>',
        ]);

        return $this->control($fieldName, $options->toArray());
    }

    /**
     * Closes an HTML form, cleans up values set by `FormHelper::create()`,
     *  and writes hidden input fields where appropriate
     * @param array $secureAttributes Secure attibutes which will be passed
     *  as HTML attributes into the hidden input elements generated for the
     *  Security Component.
     * @return string
     * @uses $inline
     */
    public function end(array $secureAttributes = [])
    {
        $this->inline = false;

        return parent::end($secureAttributes);
    }

    /**
     * Checks if the current opened form is an inline form
     * @return bool
     */
    public function isInline()
    {
        return !empty($this->inline);
    }

    /**
     * Returns a formatted `<label>` element.
     * Will automatically generate a `for` attribute if one is not provided.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param string $text Text that will appear in the label field. If is
     *  left undefined the text will be inflected from the fieldName
     * @param array|string $options HTML attributes, or a string to be used
     *  as a class name
     * @return string
     */
    public function label($fieldName, $text = null, array $options = [])
    {
        $options = new OptionsParser($options, ['escape' => false]);
        list($text, $options) = $this->Html->addIconToText($text, $options);

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
     * @uses postLink()
     */
    public function postButton($title, $url, array $options = [])
    {
        $options = new OptionsParser($options);
        $options->add('role', 'button')->addButtonClasses();

        return self::postLink($title, $url, $options->toArray());
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
    public function postLink($title, $url = null, array $options = [])
    {
        $options = new OptionsParser($options, ['escape' => false, 'title' => $title]);
        $options->add('title', trim(h(strip_tags($options->get('title')))))->tooltip();
        list($title, $options) = $this->Html->addIconToText($title, $options);

        return parent::postLink($title, $url, $options->toArray());
    }

    /**
     * Returns a formatted SELECT element
     * @param string $fieldName Name attribute of the SELECT
     * @param array|\Traversable $options Array of the OPTION elements
     *  (as 'value'=>'Text' pairs) to be used in the SELECT element
     * @param array $attributes The HTML attributes of the select element
     * @return string
     */
    public function select($fieldName, $options = [], array $attributes = [])
    {
        $attributes = new OptionsParser($attributes);

        if (!$attributes->exists('default') && !$attributes->exists('value')) {
            $attributes->Default->add('empty', true);
        }

        return parent::select($fieldName, $options, $attributes->toArray());
    }

    /**
     * Creates a submit button
     * @param string $caption The label appearing on the submit button or an
     *  image
     * @param array $options HTML attributes and options
     * @return string
     * @uses button()
     */
    public function submit($caption = null, array $options = [])
    {
        $options = new OptionsParser($options);
        $options->add('type', 'submit');

        return self::button($caption, $options->toArray());
    }

    /**
     * Creates a textarea widget
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     */
    public function textarea($fieldName, array $options = [])
    {
        $options = new OptionsParser($options, ['cols' => null, 'rows' => null]);

        return parent::textarea($fieldName, $options->toArray());
    }

    /**
     * Creates a timepicker.
     *
     * To add the scripts for timepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::timepicker()
     * @uses control()
     */
    public function timepicker($fieldName, array $options = [])
    {
        $options = new OptionsParser($options, ['data-date-format' => 'HH:mm', 'type' => 'text']);
        $options->append('templates', [
            'input' => '<input type="{{type}}" name="{{name}}" class="form-control timepicker"{{attrs}}/>',
            'inputError' => '<input type="{{type}}" name="{{name}}" class="form-control timepicker is-invalid"{{attrs}}/>',
        ]);

        return $this->control($fieldName, $options->toArray());
    }
}
