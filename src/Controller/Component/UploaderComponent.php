<?php
/** @noinspection PhpMissingReturnTypeInspection */
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
use LogicException;
use Psr\Http\Message\UploadedFileInterface;
use Tools\Filesystem;
use function Cake\I18n\__d;

/**
 * A component to upload files
 */
class UploaderComponent extends Component
{
    /**
     * First error
     * @var string|null
     */
    protected ?string $error = null;

    /**
     * Uploaded file instance
     * @var \Psr\Http\Message\UploadedFileInterface|null
     */
    protected ?UploadedFileInterface $file = null;

    /**
     * Returns the first error
     * @return string|null First error or `null` with no errors
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Internal method to set an error.
     *
     * It does not override if an error has already been set.
     * @param string $error Error
     * @return void
     */
    protected function setError(string $error): void
    {
        $this->error ??= $error;
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
     * @throws \LogicException
     */
    protected function _checkUploadedFileInformation(): void
    {
        if (!$this->getFile() instanceof UploadedFileInterface) {
            throw new LogicException(__d('me_tools', 'There are no uploaded file information'));
        }
    }

    /**
     * Checks if the mimetype is correct
     * @param string|array $acceptedMimetype Accepted mimetypes as string or array or a magic word (`images` or `text`)
     * @return self
     */
    public function mimetype(string|array $acceptedMimetype): UploaderComponent
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

        /** @var \Psr\Http\Message\UploadedFileInterface $file */
        $file = $this->getFile();
        if (!in_array($file->getClientMediaType(), (array)$acceptedMimetype)) {
            $this->setError(__d('me_tools', 'The mimetype {0} is not accepted', $file->getClientMediaType()));
        }

        return $this;
    }

    /**
     * Saves the file
     * @param string $directory Directory where you want to save the uploaded file
     * @param string|null $filename Optional filename. Otherwise, it will be generated automatically
     * @return string|false Final full path of the uploaded file or `false` on failure
     */
    public function save(string $directory, ?string $filename = null): string|false
    {
        $this->_checkUploadedFileInformation();

        //Checks for previous errors
        if ($this->getError()) {
            return false;
        }

        if (!is_writable($directory)) {
            $this->setError(__d('me_tools', 'File or directory `{0}` is not writable', rtr($directory)));

            return false;
        }

        /** @var \Psr\Http\Message\UploadedFileInterface $file */
        $file = $this->getFile();
        $filename = $filename ? basename($filename) : $this->findTargetFilename($file->getClientFilename() ?: '');
        $target = Filesystem::concatenate($directory, $filename);

        try {
            $file->moveTo($target);
        } catch (UploadedFileErrorException) {
            $this->setError(__d('me_tools', 'The file was not successfully moved to the target directory'));

            return false;
        }

        return $target;
    }

    /**
     * Returns the uploaded file instance
     * @return \Psr\Http\Message\UploadedFileInterface|null
     * @since 2.20.1
     */
    public function getFile(): ?UploadedFileInterface
    {
        return $this->file;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as `$this->getRequest()->getData('file')`)
     * @param \Psr\Http\Message\UploadedFileInterface|array $file Uploaded file information
     * @return $this
     * @since 2.20.1
     */
    public function setFile(UploadedFileInterface|array $file)
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
}
