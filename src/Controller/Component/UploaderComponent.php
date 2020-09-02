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
use RuntimeException;
use Tools\Exceptionist;

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
     * Internal method to set an error
     * @param string $error Error
     * @return void
     * @uses $error
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
        //If the file already exists, adds a numeric suffix
        if (file_exists($target)) {
            $dirname = dirname($target) . DS;
            $filename = pathinfo($target, PATHINFO_FILENAME);
            $extension = pathinfo($target, PATHINFO_EXTENSION);

            //Initial tmp name
            $tmp = $dirname . $filename;

            for ($i = 1;; $i++) {
                $target = $tmp . '_' . $i;

                if ($extension) {
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
     * Returns the first error
     * @return string|null First error or `null` with no errors
     * @uses $error
     */
    public function getError(): ?string
    {
        return $this->error ?: null;
    }

    /**
     * Checks if the mimetype is correct
     * @param string|array $acceptedMimetype Accepted mimetypes as string or
     *  array or a magic word (`images` or `text`)
     * @return $this
     * @throws \RuntimeException
     * @uses setError()
     * @uses $file
     */
    public function mimetype($acceptedMimetype)
    {
        Exceptionist::isTrue(
            $this->file instanceof UploadedFileInterface,
            __d('me_tools', 'There are no uploaded file information'),
            RuntimeException::class
        );

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
     * @return string|bool Final full path of the uploaded file or `false` on
     *  failure
     * @throws \RuntimeException
     * @uses findTargetFilename()
     * @uses getError()
     * @uses setError()
     * @uses $file
     */
    public function save(string $directory, ?string $filename = null)
    {
        Exceptionist::isTrue(
            $this->file instanceof UploadedFileInterface,
            __d('me_tools', 'There are no uploaded file information'),
            RuntimeException::class
        );

        //Checks for previous errors
        if ($this->getError()) {
            return false;
        }

        Exceptionist::isDir($directory, RuntimeException::class);

        $filename = $filename ? basename($filename) : $this->findTargetFilename($this->file->getClientFilename());
        $target = add_slash_term($directory) . $filename;

        try {
            $this->file->moveTo($target);
        } catch (UploadedFileErrorException $e) {
            $this->setError(__d('me_tools', 'The file was not successfully moved to the target directory'));

            return false;
        }

        return $target;
    }

    /**
     * Sets uploaded file information (`$_FILES` array, better as
     *  `$this->getRequest()->getData('file')`)
     * @param \Psr\Http\Message\UploadedFileInterface|array $file Uploaded file information
     * @return $this
     * @uses setError()
     * @uses $error
     * @uses $file
     */
    public function set($file)
    {
        //Resets `$error`
        unset($this->error);

        $this->file = $file instanceof UploadedFileInterface ? $file : new UploadedFile($file['tmp_name'], $file['size'], $file['error'], $file['name'], $file['type']);

        //Checks errors during upload
        if ($this->file->getError() !== UPLOAD_ERR_OK) {
            $this->setError(UploadedFile::ERROR_MESSAGES[$this->file->getError()]);
        }

        return $this;
    }
}
