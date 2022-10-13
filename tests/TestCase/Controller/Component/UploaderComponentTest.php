<?php
/** @noinspection PhpUnhandledExceptionInspection */
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

use Laminas\Diactoros\Exception\UploadedFileErrorException;
use Laminas\Diactoros\UploadedFile;
use MeTools\Controller\Component\UploaderComponent;
use MeTools\TestSuite\ComponentTestCase;
use Psr\Http\Message\UploadedFileInterface;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\Filesystem;

/**
 * UploaderComponentTest class
 * @property \MeTools\Controller\Component\UploaderComponent $Component
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
        $file = Filesystem::instance()->createTmpFile();

        return new UploadedFile($file, filesize($file) ?: 0, $error, basename($file), 'text/plain');
    }

    /**
     * Called after every test method
     * @return void
     */
    protected function tearDown(): void
    {
        Filesystem::instance()->unlinkRecursive(UPLOADS);
        Filesystem::instance()->rmdirRecursive(TMP . 'upload_test');

        parent::tearDown();
    }

    /**
     * Tests for `getError()` and `setError()` methods
     * @test
     */
    public function testGetErrorAndSetError(): void
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
    public function testFindTargetFilename(): void
    {
        $Filesystem = new Filesystem();
        $findTargetFilenameMethod = fn(string $filename): string => $this->invokeMethod($this->Component, 'findTargetFilename', [$filename]);

        $file1 = UPLOADS . 'target.txt';
        $file2 = UPLOADS . 'target_1.txt';
        $file3 = UPLOADS . 'target_2.txt';

        $this->assertEquals($file1, $findTargetFilenameMethod($file1));

        //Creates the first file
        $Filesystem->createFile($file1);
        $this->assertEquals($file2, $findTargetFilenameMethod($file1));

        //Creates the second file
        $Filesystem->createFile($file2);
        $this->assertEquals($file3, $findTargetFilenameMethod($file1));

        //Files without extension
        $file1 = UPLOADS . 'target';
        $file2 = UPLOADS . 'target_1';
        $this->assertEquals($file1, $findTargetFilenameMethod($file1));

        //Creates the first file
        $Filesystem->createFile($file1);
        $this->assertEquals($file2, $findTargetFilenameMethod($file1));
    }

    /**
     * Tests for `getFile()` and `setFile()` methods
     * @uses \MeTools\Controller\Component\UploaderComponent::getFile()
     * @uses \MeTools\Controller\Component\UploaderComponent::setFile()
     * @test
     */
    public function testGetAndSetFile(): void
    {
        $result = $this->Component->setFile($this->createFile());
        $this->assertInstanceOf(UploaderComponent::class, $result);
        $this->assertInstanceOf(UploadedFileInterface::class, $this->Component->getFile());
        $this->assertEmpty($this->Component->getError());

        $this->Component->setFile($this->createFile(UPLOAD_ERR_INI_SIZE));
        $this->assertNotEmpty($this->Component->getError());
    }

    /**
     * Tests for `set()` method, with file as array
     * @test
     */
    public function testSetWithFileAsArray(): void
    {
        $file = Filesystem::instance()->createTmpFile();
        $this->Component->setFile([
            'name' => basename($file),
            'type' => mime_content_type($file),
            'tmp_name' => $file,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($file),
        ]);
        $this->assertInstanceOf(UploadedFileInterface::class, $this->Component->getFile());
        $this->assertEmpty($this->Component->getError());
    }

    /**
     * Test for `mimetype()` method
     * @uses \MeTools\Controller\Component\UploaderComponent::mimetype()
     * @test
     */
    public function testMimetype(): void
    {
        $this->Component->setFile($this->createFile());

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
        $this->expectException(ObjectWrongInstanceException::class);
        $this->expectExceptionMessage('There are no uploaded file information');
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $this->getMockForComponent(UploaderComponent::class, [])->mimetype('text/plain');
    }

    /**
     * Test for `save()` method
     * @uses \MeTools\Controller\Component\UploaderComponent::save()
     * @test
     */
    public function testSave(): void
    {
        foreach ([UPLOADS, rtrim(UPLOADS, DS)] as $targetDirectory) {
            $this->Component->setFile($this->createFile());
            $result = $this->Component->save($targetDirectory) ?: '';
            $this->assertStringStartsWith(UPLOADS, $result);
            $this->assertEmpty($this->Component->getError());
            $this->assertFileExists($result);
        }

        foreach (['customFilename', 'customFilename.txt', TMP . 'customFilename.txt'] as $targetFilename) {
            $this->Component->setFile($this->createFile());
            $result = $this->Component->save(UPLOADS, $targetFilename) ?: '';
            $this->assertEquals(UPLOADS . basename($targetFilename), $result);
            $this->assertEmpty($this->Component->getError());
            $this->assertFileExists($result);
        }

        $this->assertFalse($this->Component->setFile($this->createFile())->save(DS . 'noExisting'));
        $this->assertSame('File or directory `' . DS . 'noExisting` is not writable', $this->Component->getError());

        //With file not successfully moved to the target directory
        $file = Filesystem::instance()->createTmpFile();
        $UploadedFile = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([$file, filesize($file), UPLOAD_ERR_OK, basename($file), 'text/plain'])
            ->onlyMethods(['moveTo'])
            ->getMock();

        $UploadedFile->method('moveTo')->willThrowException(new UploadedFileErrorException());

        $this->assertFalse($this->Component->setFile($UploadedFile)->save(UPLOADS));
        $this->assertSame('The file was not successfully moved to the target directory', $this->Component->getError());

        //With no file
        $this->expectException(ObjectWrongInstanceException::class);
        $this->expectExceptionMessage('There are no uploaded file information');
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $this->getMockForComponent(UploaderComponent::class, [])->save('');
    }

    /**
     * Test for `save()` method, with an error
     * @uses \MeTools\Controller\Component\UploaderComponent::save()
     * @test
     */
    public function testSaveWithError(): void
    {
        $this->Component->setFile($this->createFile());

        //Sets an error
        $error = 'error before save';
        $this->invokeMethod($this->Component, 'setError', [$error]);
        $this->assertFalse($this->Component->save(UPLOADS));
        $this->assertEquals($error, $this->Component->getError());
    }
}
