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
 * @since       2.25.0
 */

namespace MeTools\Model\Validation;

use Cake\Validation\Validator;
use Closure;
use function Cake\I18n\__d;

/**
 * Validator object encapsulates all methods related to data validations for a model.
 *
 * This class provides additional methods compared to the base one.
 *
 * Remember to set this validator as the default validator in the table class or make your table extend `AppTable`.
 * @see https://book.cakephp.org/5/en/orm/validation.html#default-validator-class
 * @see \MeTools\Test\TestCase\Model\Table\AppTable
 */
class AppValidator extends Validator
{
    /**
     * Allow empty string for `$field` when `$secondField` is empty (or is not set)
     * @param string $field The field you want to apply the rule to.
     * @param string $secondField Another field to check $field against
     * @param string|null $message The error message when the rule fails.
     * @return self
     * @since 2.25.4
     */
    public function allowEmptyStringOnEmptyField(string $field, string $secondField, ?string $message = null): AppValidator
    {
        return $this->allowEmptyString($field, $message, fn(array $context) => empty($context['data'][$secondField]));
    }

    /**
     * Add a rule that ensure a string begins with a capitalized letter.
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @return self
     * @since 2.25.4
     */
    public function firstLetterCapitalized(string $field, ?string $message = null, Closure|string|null $when = null): AppValidator
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'firstLetterCapitalized', $extra + [
            'message' => __d('me_tools', 'Has to begin with a capital letter'),
            'rule' => fn($value): bool => is_string($value) && ctype_upper($value[0] ?? ''),
        ]);
    }

    /**
     * Add a rule that ensure a string does not contain a reserved word
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *    true when the validation rule should be applied.
     * @return self
     */
    public function notReservedWords(string $field, ?string $message = null, Closure|string|null $when = null): AppValidator
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'notReservedWords', $extra + [
            'message' => __d('me_tools', 'This value contains a reserved word'),
            'rule' => ['custom', '/^((?!admin|manager|root|supervisor|moderator|pwd|password).)+$/i'],
        ]);
    }

    /**
     * Add a rule that ensures a string length is within a range
     * @param string $field The field you want to apply the rule to.
     * @param array $range The inclusive minimum and maximum length you want permitted.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *    true when the validation rule should be applied.
     * @return static
     * @throws \InvalidArgumentException
     * @see Validator::lengthBetween()
     */
    public function lengthBetween(string $field, array $range, ?string $message = null, Closure|string|null $when = null): Validator
    {
        $message = $message ?: __d('me_tools', 'Must be between {0} and {1} chars', array_value_first($range), array_value_last($range));

        return parent::lengthBetween($field, $range, $message, $when);
    }

    /**
     * Adds a rule that ensures a string is a valid "person name" (letters, apostrophe, space. Has to begin with a
     *  capital letter).
     *
     * This rule can be applied, for example, to the first or last name of a `User` or `Customer`.
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *    true when the validation rule should be applied.
     * @return self
     */
    public function personName(string $field, ?string $message = null, Closure|string|null $when = null): AppValidator
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'personName', $extra + [
            'message' => sprintf(
                '%s: %s. %s',
                __d('me_tools', 'Allowed chars'),
                __d('me_tools', 'letters, apostrophe, space'),
                __d('me_tools', 'Has to begin with a capital letter')
            ),
            'rule' => ['custom', '/^[A-Z][A-zàèéìòù\'\ ]+$/'],
        ]);
    }

    /**
     * Adds a rule that ensure is valid "slug" (lowercase letters, numbers, dash. Has to begin with a lowercase letter)
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *    true when the validation rule should be applied.
     * @return self
     */
    public function slug(string $field, ?string $message = null, Closure|string|null $when = null): AppValidator
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'slug', $extra + [
            'message' => sprintf(
                '%s: %s. %s',
                __d('me_tools', 'Allowed chars'),
                __d('me_tools', 'lowercase letters, numbers, dash'),
                __d('me_tools', 'Has to begin with a lowercase letter')
            ),
            'rule' => ['custom', '/^[a-z][a-z\d\-]+$/'],
        ]);
    }

    /**
     * Adds a rule that ensures a string is a valid "title" (letters, numbers, apostrophe, space, slash, dash,
     *  parentheses. Has to begin with a capital letter).
     *
     * This rule can be applied, for example, to the `title` of a `Post` or a `Category`.
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *    true when the validation rule should be applied.
     * @return self
     */
    public function title(string $field, ?string $message = null, Closure|string|null $when = null): AppValidator
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'title', $extra + [
            'message' => sprintf(
                '%s: %s. %s',
                __d('me_tools', 'Allowed chars'),
                __d('me_tools', 'letters, numbers, apostrophe, space, slash, dash, parentheses, comma'),
                __d('me_tools', 'Has to begin with a capital letter')
            ),
            'rule' => ['custom', '/^[A-Z][A-zàèéìòù\d\'\ \/\-\(\),]+$/'],
        ]);
    }

    /**
     * Adds a rule that ensure a password is a valid password
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param \Closure|string|null $when Either 'create' or 'update' or a callable that returns
     *    true when the validation rule should be applied.
     * @return self
     */
    public function validPassword(string $field, ?string $message = null, Closure|string|null $when = null): AppValidator
    {
        $this->minLength($field, 8, $message ?: __d('me_tools', 'Must be at least {0} chars', 8), $when);

        $extra = array_filter(['on' => $when, 'message' => $message]);

        $this->add($field, 'hasDigit', $extra + [
            'message' => __d('me_tools', 'Must contain at least one digit'),
            'rule' => ['custom', '/\d/'],
        ]);

        $this->add($field, 'hasLowercaseLetter', $extra + [
            'message' => __d('me_tools', 'Must contain at least one lowercase letter'),
            'rule' => ['custom', '/[a-z]/'],
        ]);

        $this->add($field, 'hasCapitalLetter', $extra + [
            'message' => __d('me_tools', 'Must contain at least one capital letter'),
            'rule' => ['custom', '/[A-Z]/'],
        ]);

        $this->notAlphaNumeric($field, $message ?: __d('me_tools', 'Must contain at least one symbol'), $when);

        return $this;
    }
}
