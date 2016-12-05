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
 */
namespace MeTools\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\TestSuite\TestCase;
use MeTools\Controller\Component\UploaderComponent as BaseUploaderComponent;

/**
 * Makes public some protected methods/properties from `UploaderComponent`
 */
class UploaderComponent extends BaseUploaderComponent
{
    protected function move_uploaded_file($filename, $destination)
    {
        //@codingStandardsIgnoreLine
        return @rename($filename, $destination);
    }

    public function getFile()
    {
        if (!isset($this->file)) {
            return false;
        }

        return $this->file;
    }

    public function findTargetFilename($target)
    {
        return $this->_findTargetFilename($target);
    }

    public function resetError()
    {
        unset($this->error);
    }

    public function setError($error)
    {
        return $this->_setError($error);
    }
}

/**
 * UploaderComponentTest class
 */
class UploaderComponentTest extends TestCase
{
    /**
     * @var \UploaderComponent
     */
    protected $Uploader;

    /**
     * Internal method to create a file and get a valid array for upload
     * @return array
     */
    protected function _createFile()
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

        $controller = new Controller(new Request());
        $componentRegistry = new ComponentRegistry($controller);
        $this->Uploader = new UploaderComponent($componentRegistry);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        //Deletes all files
        foreach (glob(UPLOADS . '*') as $file) {
            unlink($file);
        }

        unset($this->Uploader);
    }

    /**
     * Tests for `_setError()` and `error()` methods
     * @test
     */
    public function testErrorAndSetError()
    {
        $this->assertFalse($this->Uploader->error());

        $this->Uploader->setError('first');
        $this->assertEquals('first', $this->Uploader->error());

        //It sets only the first error
        $this->Uploader->setError('second');
        $this->assertEquals('first', $this->Uploader->error());
    }

    /**
     * Tests for `_findTargetFilename()` method
     * @test
     */
    public function testFindTargetFilename()
    {
        $file1 = UPLOADS . 'target.txt';
        $file2 = UPLOADS . 'target_1.txt';
        $file3 = UPLOADS . 'target_2.txt';

        $this->assertEquals($file1, $this->Uploader->findTargetFilename($file1));

        //Creates the first file
        file_put_contents($file1, null);
        $this->assertEquals($file2, $this->Uploader->findTargetFilename($file1));

        //Creates the second file
        file_put_contents($file2, null);
        $this->assertEquals($file3, $this->Uploader->findTargetFilename($file1));

        //Files without extension
        $file1 = UPLOADS . 'target';
        $file2 = UPLOADS . 'target_1';

        $this->assertEquals($file1, $this->Uploader->findTargetFilename($file1));

        //Creates the first file
        file_put_contents($file1, null);
        $this->assertEquals($file2, $this->Uploader->findTargetFilename($file1));
    }

    /**
     * Tests for `set()` method
     * @test
     * @uses _createFile()
     */
    public function testSet()
    {
        $file = $this->_createFile();

        $this->Uploader->set($file);
        $this->assertEmpty($this->Uploader->error());
        $this->assertEquals('stdClass', get_class($this->Uploader->getFile()));
        $this->assertEquals(
            ['name', 'type', 'tmp_name', 'error', 'size'],
            array_keys((array)$this->Uploader->getFile())
        );

        $this->Uploader->set(array_merge($file, ['error' => UPLOAD_ERR_INI_SIZE]));
        $this->assertNotEmpty($this->Uploader->error());
        $this->assertFalse($this->Uploader->getFile());

        $this->Uploader->set(array_merge($file, ['error' => 'noExistingErrorCode']));
        $this->assertEquals('Unknown upload error', $this->Uploader->error());
        $this->assertFalse($this->Uploader->getFile());
    }

    /**
     * Test for `mimetype()` method
     * @test
     * @uses _createFile()
     */
    public function testMimetype()
    {
        $file = $this->_createFile();
        $this->Uploader->set($file);

        $this->Uploader->mimetype('text/plain');
        $this->assertEmpty($this->Uploader->error());

        $this->Uploader->mimetype('text');
        $this->assertEmpty($this->Uploader->error());

        $this->Uploader->mimetype(['text/plain', 'image/gif']);
        $this->assertEmpty($this->Uploader->error());

        $this->Uploader->mimetype('image/gif');
        $this->assertEquals('The mimetype image/gif is not accepted', $this->Uploader->error());

        //Resets error
        $this->Uploader->resetError();

        $this->Uploader->mimetype(['image/gif', 'image/png']);
        $this->assertEquals('The mimetype image/gif, image/png is not accepted', $this->Uploader->error());
    }

    /**
     * Test for `mimetype()` method, with no file
     * @expectedException Cake\Network\Exception\InternalErrorException
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
     * @uses _createFile()
     */
    public function testSave()
    {
        $file = $this->_createFile();
        $this->assertFileExists($file['tmp_name']);
        $this->Uploader->set($file);

        $result = $this->Uploader->save(UPLOADS);
        $this->assertFalse($this->Uploader->error());
        $this->assertFileExists($result);
        $this->assertFileNotExists($file['tmp_name']);
    }

    /**
     * Test for `save()` method, with a not writable directory
     * @test
     * @uses _createFile()
     */
    public function testSaveNoWritableDir()
    {
        //Creates a non-writable directory
        //@codingStandardsIgnoreLine
        @mkdir(TMP . 'noWritableDir', 0444);

        $file = $this->_createFile();
        $this->Uploader->set($file);

        $this->assertFalse($this->Uploader->save(TMP . 'noWritableDir'));
        $this->assertEquals('The file was not successfully moved to the target directory', $this->Uploader->error());
    }

    /**
     * Test for `save()` method, using a no existing directory
     * @expectedException Cake\Network\Exception\InternalErrorException
     * @expectedExceptionMessage Invalid or no existing directory /tmp/uploads/noExistingDir
     * @test
     * @uses _createFile()
     */
    public function testSaveNoExistingDir()
    {
        $file = $this->_createFile();
        $this->Uploader->set($file)->save(UPLOADS . 'noExistingDir');
    }

    /**
     * Test for `save()` method, with no file
     * @expectedException Cake\Network\Exception\InternalErrorException
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
     * @uses _createFile()
     */
    public function testSaveWithError()
    {
        $file = $this->_createFile();
        $this->Uploader->set($file);

        //Sets an error
        $error = 'error before save';
        $this->Uploader->setError($error);

        $this->assertFalse($this->Uploader->save(UPLOADS));
        $this->assertEquals($error, $this->Uploader->error());
    }
}
