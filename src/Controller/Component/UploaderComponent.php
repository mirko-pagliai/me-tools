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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			https://www.google.com/recaptcha reCAPTCHA site
 */
namespace MeTools\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\InternalErrorException;

/**
 * A component to upload files
 */
class UploaderComponent extends Component {
    /**
     * Error.
     * It can be set by various methods.
     * @var mixed String or `FALSE`
     */
    protected $error = FALSE;
    
    /**
     * Uploaded file information
     * @see set()
     * @var array
     */
    protected $file;
        
    /**
     * Internal method to set an error.
     * It sets only the first error.
     * @param string $error
     * @uses $error
     */
    protected function _setError($error) {
        if(empty($this->error)) {
            $this->error = $error;
        }
    }

    /**
     * Internal method to set the target
     * @param string $target
     * @return string
     * @uses $file
     */
    protected function _setTarget($target) {
        //If the target is a directory, then the filename will be unchanged
        if(is_dir($target)) {
            //Adds slash term
            if(!Folder::isSlashTerm($target)) {
                $target .= DS;
            }
            
            $target .= $this->file->name;
        }
        
        //If the file already exists, adds a numeric suffix
        if(file_exists($target)) {
            //Filename (without extension)
            $filename = pathinfo($this->file->name, PATHINFO_FILENAME);
            $target = dirname($target);
            
            for($i = 1; ; $i++) {
                $tmp = $target.DS.sprintf('%s_%s.%s', $filename, $i, $this->file->extension);
                
                if(!file_exists($tmp)) {
                    $target = $tmp;
                    break;
                }
            }
        }
        
        return $target;
    }
    
    /**
     * Returns the first error
     * @return mixed String or `FALSE`
     * @uses $error
     */
    public function error() {
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
    public function mimetype($mimetype) {
        if($mimetype === 'image') {
            $mimetype = ['image/gif', 'image/jpeg', 'image/png'];
        }
        
        if(!in_array($this->file->type, (array) $mimetype)) {
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
     * @return mixed Target path or `FALSE` on failure
     * @uses _setError()
     * @uses _setTarget()
     * @uses error()
     * @uses $file
     */
    public function save($target) {
        if(empty($this->file)) {
			throw new InternalErrorException(__d('me_tools', 'There are no uploaded file information'));
        }
        
        //Checks for previous errors
        if($this->error()) {
            return FALSE;
        }
        
        $target = $this->_setTarget($target);
        
        if(!move_uploaded_file($this->file->tmp_name, $target)) {
            $this->_setError(__d('me_tools', 'The file was not successfully moved to the target directory'));
            return FALSE;
        }
        
        return $target;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as 
     *  `$this->request->data('file')`)
     * @param array $file Uploaded file information
     * @return \MeCms\Controller\Component\UploaderComponent
     * @uses _setError()
     * @uses $file
     */
    public function set($file) {
        $this->file = (object) $file;
        
        //Checks errors during upload
        if($this->file->error !== UPLOAD_ERR_OK) {
            switch($this->file->error) { 
                case UPLOAD_ERR_INI_SIZE:
                    $message = "The uploaded file exceeds the maximum size "
                    . "that was specified in php.ini";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $message = "The uploaded file exceeds the maximum size "
                    . "that was specified in the HTML form";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = "The uploaded file was partially uploaded";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = "Missing a temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = "File upload stopped by extension";
                    break;
                default:
                    $message = "Unknown upload error";
                    break;
            }
            
            $this->_setError($message);
            return $this;
        }
        
        //Adds the file extension
        $this->file->extension = pathinfo($this->file->name, PATHINFO_EXTENSION);
        
        return $this;
    }
}