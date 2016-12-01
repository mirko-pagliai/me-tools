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
     * Error.
     * It can be set by various methods.
     * @var string
     */
    protected $error;

    /**
     * Uploaded file information
     * @var object
     */
    protected $file;

    /**
     * Internal method to set an error.
     * It sets only the first error.
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
            list($dirname,, $extension, $filename) = array_values(pathinfo($target));

            for ($i = 1;; $i++) {
                $tmp = $dirname . DS . sprintf('%s_%s.%s', $filename, $i, $extension);

                if (!file_exists($tmp)) {
                    $target = $tmp;

                    break;
                }
            }
        }

        return $target;
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
     * Sets and checks that the mimetype is correct
     * @param mixed $mimetype Supported mimetypes as string or array or a
     *  magic word (eg. `images`)
     * @return \MeCms\Controller\Component\UploaderComponent
     * @uses _setError()
     * @uses $file
     */
    public function mimetype($mimetype)
    {
        if ($mimetype === 'image') {
            $mimetype = ['image/gif', 'image/jpeg', 'image/png'];
        }

        if (!in_array($this->file->type, (array)$mimetype)) {
            $this->_setError(__d('me_tools', 'The mimetype {0} is not accepted', $this->file->type));
        }

        return $this;
    }

    /**
     * Saves the file.
     *
     * If you specify only a directory as target, it will keep the original
     *  filename of the file.
     * @param string $target Target
     * @return mixed Target path or `false` on failure
     * @uses _findTargetFilename()
     * @uses _setError()
     * @uses error()
     * @uses $file
     */
    public function save($target)
    {
        if (empty($this->file)) {
            throw new InternalErrorException(__d('me_tools', 'There are no uploaded file information'));
        }

        //Checks for previous errors
        if ($this->error()) {
            return false;
        }

        //If the target is a directory, then adds the filename
        if (is_dir($target)) {
            //Adds slash term
            if (!Folder::isSlashTerm($target)) {
                $target .= DS;
            }

            $target .= $this->file->name;
        }

        $target = $this->_findTargetFilename($target);

        if (!move_uploaded_file($this->file->tmp_name, $target)) {
            $this->_setError(__d('me_tools', 'The file was not successfully moved to the target directory'));

            return false;
        }

        return $target;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as
     *  `$this->request->data('file')`)
     * @param array $file Uploaded file information
     * @return \MeCms\Controller\Component\UploaderComponent
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
