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

namespace MeTools\Test\TestCase\Model\Validation;

use MeTools\Model\Validation\AppValidator;
use MeTools\TestSuite\TestCase;

/**
 * AppValidatorTest class
 */
class AppValidatorTest extends TestCase
{
    /**
     * @var AppValidator
     */
    protected AppValidator $Validator;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Validator = new AppValidator();
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::lengthBetween()
     */
    public function testLengthBetween(): void
    {
        $this->Validator->lengthBetween('username', [3, 20]);

        $expected = ['username' => ['lengthBetween' => 'Must be between 3 and 20 chars']];
        foreach (['', 'aa', str_repeat('a', 21)] as $username) {
            $this->assertSame($expected, $this->Validator->validate(compact('username')));
        }

        $this->assertEmpty($this->Validator->validate(['username' => 'validUsername']));

        //With wrong numbers
        $this->expectExceptionMessage('The $range argument requires 2 numbers');
        $this->Validator->lengthBetween('username', [1]);
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::notReservedWords()
     */
    public function testNotReservedWords(): void
    {
        $this->Validator->notReservedWords('username');

        $expected = ['username' => ['notReservedWords' => 'This value contains a reserved word']];
        foreach (['adMin' , '1manager2', 'root', 'supervisor', 'moderator', '!pwd!', 'password'] as $username) {
            $this->assertSame($expected, $this->Validator->validate(compact('username')));
            $this->assertSame($expected, $this->Validator->validate(['username' => 'a' . $username . 'a']));
        }

        $this->assertEmpty($this->Validator->validate(['username' => 'validUsername']));
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::personName()
     */
    public function testPersonName(): void
    {
        $this->Validator->personName('name');

        $expected = ['name' => ['personName' => 'Allowed chars: letters, apostrophe, space. Has to begin with a capital letter']];
        foreach (['mirko', 'Mirk-o', 'Mirko_0', ' Mirko', '\'Mirko', 'mirkO', 'Mirko1'] as $name) {
            $this->assertSame($expected, $this->Validator->validate(compact('name')));
        }

        foreach (['Mirko', 'Di Alessandro', 'D\'Alessandro'] as $name) {
            $this->assertEmpty($this->Validator->validate(compact('name')));
        }
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::slug()
     */
    public function testSlug(): void
    {
        $this->Validator->slug('slug');

        $expected = ['slug' => ['slug' => 'Allowed chars: lowercase letters, numbers, dash. Has to begin with a lowercase letter']];
        foreach (['Mirko', 'mirko pagliai', 'mirko_pagliai', '-mirko', '3mirko'] as $slug) {
            $this->assertSame($expected, $this->Validator->validate(compact('slug')));
        }

        foreach (['mirko', 'mirko-pagliai', 'mirko3', 'mirko-3', 'mirko-pagliai-3'] as $slug) {
            $this->assertEmpty($this->Validator->validate(compact('slug')));
        }
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::validPassword()
     */
    public function testValidPassword(): void
    {
        $this->Validator->validPassword('password');

        $expected = ['password' => ['minLength' => 'Must be at least 8 chars']];
        $this->assertSame($expected, $this->Validator->validate(['password' => 'aa']));

        $expected = ['password' => [
            'hasDigit' => 'Must contain at least one digit',
            'hasCapitalLetter' => 'Must contain at least one capital letter',
            'notAlphaNumeric' => 'Must contain at least one symbol',
        ]];
        $this->assertSame($expected, $this->Validator->validate(['password' => str_repeat('a', 8)]));

        unset($expected['password']['hasCapitalLetter']);
        $expected['password'] += ['hasLowercaseLetter' => 'Must contain at least one lowercase letter'];
        $this->assertEquals($expected, $this->Validator->validate(['password' => str_repeat('A', 8)]));

        $this->assertEmpty($this->Validator->validate(['password' => 'aaaAAA1$']));

        //With a custom message
        $this->Validator->validPassword('password', 'Your password is wrong!');
        $expected = ['password' => ['minLength' => 'Your password is wrong!']];
        $this->assertSame($expected, $this->Validator->validate(['password' => 'aa']));
    }
}
