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
namespace MeTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\InternalErrorException;

/**
 * A component to upload files
 */
class UploaderComponent extends Component
{
    /**
     * Last error
     * @var string
     */
    protected $error;

    /**
     * Uploaded file information
     * @var object
     */
    protected $file;

    /**
     * Internal method to set an error
     * @param string $error Error
     * @return void
     * @uses $error
     */
    protected function _setError($error)
    {
        if (empty($this->error)) {
            $this->error = $error;
        }
    }

    /**
     * Internal method to find the target filename
     * @param string $target Path
     * @return string
     */
    protected function _findTargetFilename($target)
    {
        //If the file already exists, adds a numeric suffix
        if (file_exists($target)) {
            $dirname = dirname($target) . DS;
            $filename = pathinfo($target, PATHINFO_FILENAME);
            $extension = pathinfo($target, PATHINFO_EXTENSION);

            //Initial tmp name
            $tmp = $dirname . $filename;

            for ($i = 1;; $i++) {
                $target = $tmp . '_' . $i;

                if (!empty($extension)) {
                    $target .= '.' . $extension;
                }

                if (!file_exists($target)) {
                    break;
                }
            }
        }

        return $target;
    }

    /**
     * This allows you to override the `move_uploaded_file()` function, for
     *  example with the `rename()` function
     * @param string $filename The filename of the uploaded file
     * @param string $destination The destination of the moved file
     * @return bool
     */
    //@codingStandardsIgnoreLine
    protected function move_uploaded_file($filename, $destination)
    {
        return move_uploaded_file($filename, $destination);
    }

    /**
     * Returns the first error
     * @return mixed String or `false`
     * @uses $error
     */
    public function error()
    {
        if (!isset($this->error)) {
            return false;
        }

        return $this->error;
    }

    /**
     * Checks if the mimetype is correct
     * @param string|array $mimetype Supported mimetypes as string or array or
     *  a magic word (eg. `images`)
     * @return \MeTools\Controller\Component\UploaderComponent
     * @throws InternalErrorException
     * @uses _setError()
     * @uses $file
     */
    public function mimetype($mimetype)
    {
        if (empty($this->file)) {
            throw new InternalErrorException(__d('me_tools', 'There are no uploaded file information'));
        }

        switch ($mimetype) {
            case 'image':
                $mimetype = ['image/gif', 'image/jpeg', 'image/png'];
                break;
            case 'text':
                $mimetype = ['text/plain'];
                break;
        }

        $mimetype = (array)$mimetype;

        if (!in_array(mime_content_type($this->file->tmp_name), $mimetype)) {
            $this->_setError(__d('me_tools', 'The mimetype {0} is not accepted', implode(', ', $mimetype)));
        }

        return $this;
    }

    /**
     * Saves the file
     * @param string $directory Directory where you want to save the uploaded
     *  file
     * @return string|bool Final full path of the uploaded file or `false` on
     *  failure
     * @uses _findTargetFilename()
     * @uses _setError()
     * @uses error()
     * @uses move_uploaded_file()
     * @uses $file
     */
    public function save($directory)
    {
        if (empty($this->file)) {
            throw new InternalErrorException(__d('me_tools', 'There are no uploaded file information'));
        }

        //Checks for previous errors
        if ($this->error()) {
            return false;
        }

        if (!is_dir($directory)) {
            throw new InternalErrorException(__d('me_tools', 'Invalid or no existing directory {0}', $directory));
        }

        //Adds slash term
        if (!Folder::isSlashTerm($directory)) {
            $directory .= DS;
        }

        //Gets the target full path
        $file = $this->_findTargetFilename($directory . DS . $this->file->name);

        if (!$this->move_uploaded_file($this->file->tmp_name, $file)) {
            $this->_setError(__d('me_tools', 'The file was not successfully moved to the target directory'));

            return false;
        }

        return $file;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as
     *  `$this->request->getData('file')`)
     * @param array $file Uploaded file information
     * @return \MeTools\Controller\Component\UploaderComponent
     * @uses _setError()
     * @uses $error
     * @uses $file
     */
    public function set($file)
    {
        //Resets `$file` and `$error`
        unset($this->error, $this->file);

        //Errors messages
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the maximum size that was specified in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the maximum size that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            'default' => 'Unknown upload error',
        ];

        //Checks errors during upload
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            //Gets the default error message, if the error can not be
            //  identified or if the key is not present
            if (!isset($file['error']) || !array_key_exists($file['error'], $errors)) {
                $file['error'] = 'default';
            }

            $this->_setError($errors[$file['error']]);

            return $this;
        }

        $this->file = (object)$file;

        return $this;
    }
}
