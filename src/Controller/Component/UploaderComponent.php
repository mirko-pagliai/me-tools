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
namespace MeTools\Controller\Component;

use Cake\Controller\Component;
use Laminas\Diactoros\Exception\UploadedFileErrorException;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;
use Tools\Exception\ObjectWrongInstanceException;
use Tools\Exceptionist;
use Tools\Filesystem;

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
     * Uploaded file instance
     * @var \Psr\Http\Message\UploadedFileInterface
     */
    protected $file;

    /**
     * Returns the first error
     * @return string|null First error or `null` with no errors
     */
    public function getError(): ?string
    {
        return $this->error ?: null;
    }

    /**
     * Internal method to set an error
     * @param string $error Error
     * @return void
     */
    protected function setError(string $error): void
    {
        $this->error = $this->error ?: $error;
    }

    /**
     * Internal method to find the target filename
     * @param string $target Path
     * @return string
     */
    protected function findTargetFilename(string $target): string
    {
        if (file_exists($target)) {
            //Initial tmp name. If the file already exists, adds a numeric suffix
            $tmp = dirname($target) . DS . pathinfo($target, PATHINFO_FILENAME);
            $extension = pathinfo($target, PATHINFO_EXTENSION);
            $extension = $extension ? '.' . $extension : '';
            for ($i = 1;; $i++) {
                $target = $tmp . '_' . $i . $extension;
                if (!file_exists($target)) {
                    break;
                }
            }
        }

        return $target;
    }

    /**
     * Internal method to check for uploaded file information (`$file` property)
     * @return void
     * @throws \Tools\Exception\ObjectWrongInstanceException
     */
    protected function _checkUploadedFileInformation(): void
    {
        $message = __d('me_tools', 'There are no uploaded file information');
        Exceptionist::isTrue($this->getFile(), $message, ObjectWrongInstanceException::class);
        Exceptionist::isInstanceOf($this->getFile(), UploadedFileInterface::class, $message);
    }

    /**
     * Checks if the mimetype is correct
     * @param string|array $acceptedMimetype Accepted mimetypes as string or
     *  array or a magic word (`images` or `text`)
     * @return $this
     * @throws \Tools\Exception\ObjectWrongInstanceException
     */
    public function mimetype($acceptedMimetype)
    {
        $this->_checkUploadedFileInformation();

        //Changes magic words
        switch ($acceptedMimetype) {
            case 'image':
                $acceptedMimetype = ['image/gif', 'image/jpeg', 'image/png'];
                break;
            case 'text':
                $acceptedMimetype = ['text/plain'];
                break;
        }

        if (!in_array($this->file->getClientMediaType(), (array)$acceptedMimetype)) {
            $this->setError(__d('me_tools', 'The mimetype {0} is not accepted', $this->file->getClientMediaType()));
        }

        return $this;
    }

    /**
     * Saves the file
     * @param string $directory Directory where you want to save the uploaded
     *  file
     * @param string|null $filename Optional filename. Otherwise, it will be
     *  generated automatically
     * @return string|false Final full path of the uploaded file or `false` on
     *  failure
     * @throws \Tools\Exception\ObjectWrongInstanceException
     */
    public function save(string $directory, ?string $filename = null)
    {
        $this->_checkUploadedFileInformation();

        //Checks for previous errors
        if ($this->getError()) {
            return false;
        }

        if (!is_writable($directory)) {
            $this->setError(__d('me_tools', 'File or directory `{0}` is not writable', Filesystem::instance()->rtr($directory)));

            return false;
        }

        $filename = $filename ? basename($filename) : $this->findTargetFilename($this->getFile()->getClientFilename() ?: '');
        $target = Filesystem::instance()->concatenate($directory, $filename);

        try {
            $this->getFile()->moveTo($target);
        } catch (UploadedFileErrorException $e) {
            $this->setError(__d('me_tools', 'The file was not successfully moved to the target directory'));

            return false;
        }

        return $target;
    }

    /**
     * Returns the uploaded file instance
     * @return \Psr\Http\Message\UploadedFileInterface
     * @since 2.20.1
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as
     *  `$this->getRequest()->getData('file')`)
     * @param \Psr\Http\Message\UploadedFileInterface|array $file Uploaded file information
     * @return $this
     * @since 2.20.1
     */
    public function setFile($file)
    {
        //Resets `$error`
        unset($this->error);

        if (!$file instanceof UploadedFileInterface) {
            $file = new UploadedFile($file['tmp_name'], $file['size'], $file['error'], $file['name'], $file['type']);
        }
        $this->file = $file;

        //Checks errors during upload
        if ($this->file->getError() !== UPLOAD_ERR_OK) {
            $this->setError(UploadedFile::ERROR_MESSAGES[$this->file->getError()]);
        }

        return $this;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as
     *  `$this->getRequest()->getData('file')`)
     * @param \Psr\Http\Message\UploadedFileInterface|array $file Uploaded file information
     * @return $this
     * @deprecated Use instead `setFile()`
     */
    public function set($file)
    {
        deprecationWarning('Deprecated. Use instead `setFile()`');

        return $this->setFile($file);
    }
}
