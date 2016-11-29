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
 * @see         http://api.cakephp.org/3.3/class-Cake.View.Helper.FormHelper.html FormHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\FormHelper as CakeFormHelper;
use Cake\View\View;
use MeTools\Utility\OptionsParserTrait;

/**
 * Provides functionalities for forms.
 *
 * Rewrites {@link http://api.cakephp.org/3.3/class-Cake.View.Helper.FormHelper.html FormHelper}.
 */
class FormHelper extends CakeFormHelper
{
    use OptionsParserTrait;

    /**
     * Helpers
     * @var array
     */
    public $helpers = [
        'Html' => ['className' => 'MeTools.Html'],
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
     * This method only ewrites the default configuration (`$_defaultConfig`).
     * @param Cake\View\View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     * @return void
     */
    public function __construct(View $view, $config = [])
    {
        parent::__construct($view, $config);

        //Rewrites templates
        $this->templates([
            'checkboxContainer' => '<div class="input {{type}}{{required}}">{{content}}{{help}}</div>',
            'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}} {{text}}</label>',
            'inputContainer' => '<div class="form-group input {{type}}{{required}}">{{content}}{{help}}</div>',
            'inputContainerError' => '<div class="form-group input {{type}}{{required}} has-error">{{content}}{{help}}{{error}}</div>',
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
     * @see postButton(), MeTools\View\Helper\HtmlHelper::button()
     */
    public function button($title, array $options = [])
    {
        $options = $this->optionsDefaults(['type' => 'button'], $options);

        if ($options['type'] === 'submit') {
            $options = $this->addButtonClasses($options, 'success');
        } else {
            $options = $this->addButtonClasses($options);
        }

        list($title, $options) = $this->addIconToText($title, $options);

        return parent::button($title, $options);
    }

    /**
     * Creates a checkbox input element
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     */
    public function checkbox($fieldName, array $options = [])
    {
        if (!isset($options['hiddenField']) || !empty($options['hiddenField'])) {
            $options['hiddenField'] = true;
        }

        return parent::checkbox($fieldName, $options);
    }

    /**
     * Creates a CKEditor textarea.
     *
     * To add the scripts for CKEditor, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::ckeditor()
     * @uses input()
     */
    public function ckeditor($fieldName, array $options = [])
    {
        $options = $this->optionsDefaults(['type' => 'textarea'], $options);
        $options = $this->optionsValues(['class' => 'ckeditor editor'], $options);

        return self::input($fieldName, $options);
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
        //It's a form inline if there is the `inline` option or if it contains
        //  the `form-inline` class
        if (!empty($options['inline']) ||
            (isset($options['class']) && preg_match('/form-inline/', $options['class']))
        ) {
            return self::createInline($model, $options);
        }

        return parent::create($model, $options);
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
        unset($options['inline']);

        $options = $this->optionsValues(['class' => 'form-inline'], $options);

        return parent::create($model, $options);
    }

    /**
     * Creates a datepicker input.
     *
     * To add the scripts for datepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::datepicker()
     * @uses input()
     */
    public function datepicker($fieldName, array $options = [])
    {
        $options = $this->optionsValues(['class' => 'datepicker'], $options);
        $options = $this->optionsDefaults([
            'data-date-format' => 'YYYY-MM-DD',
            'type' => 'text',
        ], $options);

        return self::input($fieldName, $options);
    }

    /**
     * Creates a datetimepicker input.
     *
     * To add the scripts for datetimepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::datetimepicker()
     * @uses input()
     */
    public function datetimepicker($fieldName, array $options = [])
    {
        $options = $this->optionsValues(['class' => 'datetimepicker'], $options);
        $options = $this->optionsDefaults([
            'data-date-format' => 'YYYY-MM-DD HH:mm',
            'type' => 'text',
        ], $options);

        return self::input($fieldName, $options);
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
     * Generates an input element complete with label and wrapper div
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @uses MeTools\View\Helper\HtmlHelper::para()
     * @uses $inline
     */
    public function input($fieldName, array $options = [])
    {
        //If the field name contains the word "password", then the field type
        //  is `password`
        if (preg_match('/password/', $fieldName)) {
            $options = $this->optionsDefaults(['type' => 'password'], $options);
        }

        //Gets the input type
        if (empty($options['type'])) {
            $type = self::_inputType($fieldName, $options);
        } else {
            $type = $options['type'];
        }

        // Adds the `form-control` class, except for checkboxes and file inputs
        if (!in_array($type, ['checkbox', 'file'])) {
            $options = $this->optionsValues(['class' => 'form-control'], $options);
        }

        if ($type === 'select' && empty($options['default']) && empty($options['value'])) {
            $options = $this->optionsDefaults(['empty' => true], $options);
        }

        //Help blocks
        //See http://getbootstrap.com/css/#forms-help-text
        if (!empty($options['help'])) {
            $options['templateVars']['help'] = implode(null, array_map(function ($tip) {
                return $this->Html->para('help-block', trim($tip));
            }, (array)$options['help']));

            unset($options['help']);
        }

        if (!empty($options['button'])) {
            //Fixes templates
            $this->templates([
                'formGroup' => '{{label}}<div class="input-group">{{input}}{{button}}</div>',
            ]);

            $options['templateVars']['button'] = $this->Html->span($options['button'], ['class' => 'input-group-btn']);

            unset($options['button']);
        }

        //If is an inline form
        if ($this->inline) {
            //By default, no help blocks or error messages
            $this->templates([
                'inputContainer' => '<div class="input form-group {{type}}{{required}}">{{content}}</div>',
                'inputContainerError' => '<div class="input form-group {{type}}{{required}} has-error">{{content}}</div>',
            ]);

            //If it is not a checkbox
            if ($type !== "checkbox") {
                if (empty($options['label'])) {
                    $options['label'] = [];
                } elseif (is_string($options['label'])) {
                    $options['label'] = ['text' => $options['label']];
                }

                $options['label'] = $this->optionsValues(['class' => 'sr-only'], $options['label']);
            }
        }

        return parent::input($fieldName, $options);
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
        $options = $this->optionsDefaults(['escape' => false], $options);
        list($text, $options) = $this->addIconToText($text, $options);

        return parent::label($fieldName, $text, $options);
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
        $options = $this->optionsValues(['role' => 'button'], $options);
        $options = $this->addButtonClasses($options);

        return self::postLink($title, $url, $options);
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
        $options = $this->optionsDefaults([
            'escape' => false,
            'title' => $title,
        ], $options);

        $options['title'] = trim(h(strip_tags($options['title'])));

        list($title, $options) = $this->addIconToText($title, $options);
        $options = $this->addTooltip($options);

        return parent::postLink($title, $url, $options);
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
        if (empty($attributes['default']) && empty($attributes['value'])) {
            $attributes = $this->optionsDefaults(['empty' => true], $attributes);
        }

        return parent::select($fieldName, $options, $attributes);
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
        $options['type'] = 'submit';

        return self::button($caption, $options);
    }

    /**
     * Creates a textarea widget
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     */
    public function textarea($fieldName, array $options = [])
    {
        $options = $this->optionsDefaults([
            'cols' => null,
            'rows' => null,
        ], $options);

        return parent::textarea($fieldName, $options);
    }

    /**
     * Creates a text input for timepicker.
     *
     * To add the scripts for timepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string
     * @see MeTools\View\Helper\LibraryHelper::timepicker()
     * @uses input()
     */
    public function timepicker($fieldName, array $options = [])
    {
        $options = $this->optionsValues(['class' => 'timepicker'], $options);
        $options = $this->optionsDefaults([
            'data-date-format' => 'HH:mm',
            'type' => 'text',
        ], $options);

        return self::input($fieldName, $options);
    }
}
