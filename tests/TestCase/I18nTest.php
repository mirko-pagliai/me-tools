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
namespace MeTools\Test\TestCase;

use Cake\I18n\I18n;
use MeTools\TestSuite\TestCase;

/**
 * I18nTest class
 */
class I18nTest extends TestCase
{
    /**
     * Tests I18n translations
     * @test
     */
    public function testI18nConstant(): void
    {
        $translator = I18n::getTranslator('me_tools', 'it');
        $this->assertSame('Modifica', $translator->translate(I18N_EDIT));
        $this->assertSame('Crea le directory di default', $translator->translate('Creates default directories'));
    }
}
