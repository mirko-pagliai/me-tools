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
 */
namespace MeTools\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Network\Request;
use MeTools\Controller\Component\UploaderComponent;
use MeTools\TestSuite\TestCase;

/**
 * UploaderComponentTest class
 */
class UploaderComponentTest extends TestCase
{
    /**
     * @var \Cake\Controller\ComponentRegistry
     */
    protected $ComponentRegistry;

    /**
     * @var \MeTools\Controller\Component\UploaderComponent
     */
    protected $Uploader;

    /**
     * Internal method to create a file and get a valid array for upload
     * @return array
     */
    protected function createFile()
    {
        //Creates a file and writes some content
        $file = tempnam(TMP, 'php_upload_');
        file_put_contents($file, 'string');

        return [
            'name' => basename($file),
            'type' => mime_content_type($file),
            'tmp_name' => $file,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($file),
        ];
    }

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->ComponentRegistry = new ComponentRegistry(new Controller(new Request));
        $this->Uploader = new UploaderComponent($this->ComponentRegistry);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        //Deletes all files
        foreach (array_merge(glob(UPLOADS . '*'), glob(TMP . 'php_upload*')) as $file) {
            safe_unlink($file);
        }
    }

    /**
     * Tests for `getError()` and `setError()` methods
     * @test
     */
    public function testGetErrorAndSetError()
    {
        $this->assertFalse($this->Uploader->getError());

        $this->invokeMethod($this->Uploader, 'setError', ['first']);
        $this->assertEquals('first', $this->Uploader->getError());

        //It sets only the first error
        $this->invokeMethod($this->Uploader, 'setError', ['second']);
        $this->assertEquals('first', $this->Uploader->getError());
    }

    /**
     * Tests for `findTargetFilename()` method
     * @test
     */
    public function testFindTargetFilename()
    {
        $file1 = UPLOADS . 'target.txt';
        $file2 = UPLOADS . 'target_1.txt';
        $file3 = UPLOADS . 'target_2.txt';

        $this->assertEquals($file1, $this->invokeMethod($this->Uploader, 'findTargetFilename', [$file1]));

        //Creates the first file
        file_put_contents($file1, null);
        $this->assertEquals($file2, $this->invokeMethod($this->Uploader, 'findTargetFilename', [$file1]));

        //Creates the second file
        file_put_contents($file2, null);
        $this->assertEquals($file3, $this->invokeMethod($this->Uploader, 'findTargetFilename', [$file1]));

        //Files without extension
        $file1 = UPLOADS . 'target';
        $file2 = UPLOADS . 'target_1';

        $this->assertEquals($file1, $this->invokeMethod($this->Uploader, 'findTargetFilename', [$file1]));

        //Creates the first file
        file_put_contents($file1, null);
        $this->assertEquals($file2, $this->invokeMethod($this->Uploader, 'findTargetFilename', [$file1]));
    }

    /**
     * Tests for `set()` method
     * @test
     */
    public function testSet()
    {
        $file = $this->createFile();

        $this->Uploader->set($file);
        $this->assertEmpty($this->Uploader->getError());
        $this->assertInstanceOf('stdClass', $this->Uploader->file);
        $this->assertObjectPropertiesEqual([
            'name',
            'type',
            'tmp_name',
            'error',
            'size',
        ], $this->Uploader->file);

        $this->Uploader->set(array_merge($file, ['error' => UPLOAD_ERR_INI_SIZE]));
        $this->assertInstanceOf('stdClass', $this->Uploader->file);
        $this->assertNotEmpty($this->Uploader->getError());

        $this->Uploader->set(array_merge($file, ['error' => 'noExistingErrorCode']));
        $this->assertInstanceOf('stdClass', $this->Uploader->file);
        $this->assertEquals('Unknown upload error', $this->Uploader->getError());
    }

    /**
     * Test for `mimetype()` method
     * @test
     */
    public function testMimetype()
    {
        $this->Uploader->set($this->createFile());

        foreach (['text/plain', 'text', ['text/plain', 'image/gif']] as $mimetype) {
            $this->Uploader->mimetype($mimetype);
            $this->assertEmpty($this->Uploader->getError());

            //Resets error
            $this->setProperty($this->Uploader, 'error', null);
        }

        foreach (['image/gif', 'image'] as $mimetype) {
            $this->Uploader->mimetype($mimetype);
            $this->assertEquals('The mimetype text/plain is not accepted', $this->Uploader->getError());

            //Resets error
            $this->setProperty($this->Uploader, 'error', null);
        }
    }

    /**
     * Test for `mimetype()` method, with no file
     * @expectedException RuntimeException
     * @expectedExceptionMessage There are no uploaded file information
     * @test
     */
    public function testMimetypeNoFile()
    {
        $this->Uploader->mimetype('text/plain');
    }

    /**
     * Test for `save()` method
     * @test
     */
    public function testSave()
    {
        $this->Uploader = $this->getMockBuilder(UploaderComponent::class)
            ->setConstructorArgs([$this->ComponentRegistry])
            ->setMethods(['move_uploaded_file'])
            ->getMock();

        $this->Uploader->method('move_uploaded_file')
            ->will($this->returnCallback(function ($filename, $destination) {
                return rename($filename, $destination);
            }));

        foreach ([
            UPLOADS,
            rtrim(UPLOADS, DS),
        ] as $targetDirectory) {
            $file = $this->createFile();
            $this->Uploader->set($file);

            $result = $this->Uploader->save($targetDirectory);
            $this->assertRegExp(sprintf('/^%sphp[\w\d\._]+$/', preg_quote(UPLOADS, '/')), $result);
            $this->assertFalse($this->Uploader->getError());
            $this->assertFileExists($result);
            $this->assertFileNotExists($file['tmp_name']);
        }

        foreach ([
            'customFilename',
            'customFilename.txt',
            TMP . 'customFilename.txt',
        ] as $targetFilename) {
            $file = $this->createFile();
            $this->Uploader->set($file);

            $result = $this->Uploader->save(UPLOADS, $targetFilename);
            $this->assertEquals(UPLOADS . basename($targetFilename), $result);
            $this->assertFalse($this->Uploader->getError());
            $this->assertFileExists($result);
            $this->assertFileNotExists($file['tmp_name']);
        }
    }

    /**
     * Test for `save()` method, with a not writable directory
     * @test
     */
    public function testSaveNoWritableDir()
    {
        $this->Uploader->set($this->createFile());

        $this->assertFalse($this->Uploader->save(DS));
        $this->assertEquals('The file was not successfully moved to the target directory', $this->Uploader->getError());
    }

    /**
     * Test for `save()` method, with no file
     * @expectedException RuntimeException
     * @expectedExceptionMessage There are no uploaded file information
     * @test
     */
    public function testSaveNoFile()
    {
        $this->Uploader->save(null);
    }

    /**
     * Test for `save()` method, with an error
     * @test
     */
    public function testSaveWithError()
    {
        $this->Uploader->set($this->createFile());

        //Sets an error
        $error = 'error before save';
        $this->invokeMethod($this->Uploader, 'setError', [$error]);

        $this->assertFalse($this->Uploader->save(UPLOADS));
        $this->assertEquals($error, $this->Uploader->getError());
    }
}
