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
namespace MeTools\Test\TestCase\Controller\Component;

use Laminas\Diactoros\UploadedFile;
use MeTools\Controller\Component\UploaderComponent;
use MeTools\TestSuite\ComponentTestCase;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

/**
 * UploaderComponentTest class
 */
class UploaderComponentTest extends ComponentTestCase
{
    /**
     * Internal method to create a file and get a `UploadedFile` instance
     * @param int $error Error for this file
     * @return UploadedFileInterface
     */
    protected function createFile(int $error = UPLOAD_ERR_OK): UploadedFileInterface
    {
        $file = create_tmp_file('string');

        return new UploadedFile($file, filesize($file), $error, basename($file), 'text/plain');
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown(): void
    {
        unlink_recursive(UPLOADS);
        rmdir_recursive(TMP . 'upload_test');

        parent::tearDown();
    }

    /**
     * Tests for `getError()` and `setError()` methods
     * @test
     */
    public function testGetErrorAndSetError()
    {
        $this->assertEmpty($this->Component->getError());

        $this->invokeMethod($this->Component, 'setError', ['first']);
        $this->assertEquals('first', $this->Component->getError());

        //It sets only the first error
        $this->invokeMethod($this->Component, 'setError', ['second']);
        $this->assertEquals('first', $this->Component->getError());
    }

    /**
     * Tests for `findTargetFilename()` method
     * @test
     */
    public function testFindTargetFilename()
    {
        $findTargetFilenameMethod = function () {
            return $this->invokeMethod($this->Component, 'findTargetFilename', func_get_args());
        };

        $file1 = UPLOADS . 'target.txt';
        $file2 = UPLOADS . 'target_1.txt';
        $file3 = UPLOADS . 'target_2.txt';

        $this->assertEquals($file1, $findTargetFilenameMethod($file1));

        //Creates the first file
        create_file($file1);
        $this->assertEquals($file2, $findTargetFilenameMethod($file1));

        //Creates the second file
        create_file($file2);
        $this->assertEquals($file3, $findTargetFilenameMethod($file1));

        //Files without extension
        $file1 = UPLOADS . 'target';
        $file2 = UPLOADS . 'target_1';
        $this->assertEquals($file1, $findTargetFilenameMethod($file1));

        //Creates the first file
        create_file($file1);
        $this->assertEquals($file2, $findTargetFilenameMethod($file1));
    }

    /**
     * Tests for `set()` method
     * @test
     */
    public function testSet()
    {
        $result = $this->Component->set($this->createFile());
        $this->assertInstanceOf(UploaderComponent::class, $result);
        $this->assertInstanceOf(UploadedFileInterface::class, $this->Component->file);
        $this->assertEmpty($this->Component->getError());

        $this->Component->set($this->createFile(UPLOAD_ERR_INI_SIZE));
        $this->assertNotEmpty($this->Component->getError());
    }

    public function testSetWithFileAsArray()
    {
        $file = create_tmp_file('string');
        $this->Component->set([
            'name' => basename($file),
            'type' => mime_content_type($file),
            'tmp_name' => $file,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($file),
        ]);
        $this->assertInstanceOf(UploadedFileInterface::class, $this->Component->file);
        $this->assertEmpty($this->Component->getError());
    }

    /**
     * Test for `mimetype()` method
     * @test
     */
    public function testMimetype()
    {
        $this->Component->set($this->createFile());

        foreach (['text/plain', 'text', ['text/plain', 'image/gif']] as $mimetype) {
            $this->Component->mimetype($mimetype);
            $this->assertEmpty($this->Component->getError());

            //Resets error
            $this->setProperty($this->Component, 'error', null);
        }

        foreach (['image/gif', 'image'] as $mimetype) {
            $this->Component->mimetype($mimetype);
            $this->assertEquals('The mimetype text/plain is not accepted', $this->Component->getError());

            //Resets error
            $this->setProperty($this->Component, 'error', null);
        }

        //With no file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There are no uploaded file information');
        $this->getMockForComponent(UploaderComponent::class, null)->mimetype('text/plain');
    }

    /**
     * Test for `save()` method
     * @test
     */
    public function testSave()
    {
        foreach ([UPLOADS, rtrim(UPLOADS, DS)] as $targetDirectory) {
            $this->Component->set($this->createFile());
            $result = $this->Component->save($targetDirectory);
            $this->assertStringStartsWith(UPLOADS, $result);
            $this->assertEmpty($this->Component->getError());
            $this->assertFileExists($result);
        }

        foreach (['customFilename', 'customFilename.txt', TMP . 'customFilename.txt'] as $targetFilename) {
            $this->Component->set($this->createFile());
            $result = $this->Component->save(UPLOADS, $targetFilename);
            $this->assertEquals(UPLOADS . basename($targetFilename), $result);
            $this->assertEmpty($this->Component->getError());
            $this->assertFileExists($result);
        }

        //With no file
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There are no uploaded file information');
        $this->getMockForComponent(UploaderComponent::class, null)->save('');
    }

    /**
     * Test for `save()` method, with a not writable directory
     * @test
     */
    public function testSaveNoWritableDir()
    {
        $this->Component->set($this->createFile());
        $this->assertFalse($this->Component->save(DS));
        $this->assertEquals('The file was not successfully moved to the target directory', $this->Component->getError());
    }

    /**
     * Test for `save()` method, with an error
     * @test
     */
    public function testSaveWithError()
    {
        $this->Component->set($this->createFile());

        //Sets an error
        $error = 'error before save';
        $this->invokeMethod($this->Component, 'setError', [$error]);
        $this->assertFalse($this->Component->save(UPLOADS));
        $this->assertEquals($error, $this->Component->getError());
    }
}
