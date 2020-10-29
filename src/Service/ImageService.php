<?php

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\File\File as FileObject;

class ImageService
{
    /**
     * @var array
     */
    private $acceptedMimeTypes = ['image/jpg' => 0, 'image/jpeg' => 1, 'image/png' => 2];

    /**
     * @var FileObject|null
     */
    private $imageFile = null;


    /**
     * @param string|null $base64Image
     * @param string|null $imageNameInDB
     *
     * @return string|null
     * @throws \Exception
     */
    public function uploadTempImage(?string $base64Image, ?string $imageNameInDB = null): ?string
    {
        if ($imageNameInDB === null || $base64Image !== null) {
            $this->imageFile = $this->validateImage($base64Image);

            return uniqid() . '.' . explode('/', $this->imageFile->getMimeType())[1];
        }

        return null;
    }

    /**
     * @param string $saveImageAbsolutePath
     * @param string $imageName
     */
    public function moveImage(string $saveImageAbsolutePath, string $imageName): void
    {
        if ($this->imageFile instanceof FileObject) {
            $this->imageFile->move($saveImageAbsolutePath, $imageName);
        }
    }

    /**
     * @param string $saveImageAbsolutePath
     * @param string $imageName
     */
    public function deleteImage(string $saveImageAbsolutePath, string $imageName): void
    {
        unlink($saveImageAbsolutePath . '/' . $imageName);
    }

    /**
     * @param string $base64string
     *
     * @return FileObject
     * @throws \Exception
     */
    private function validateImage(string $base64string): FileObject
    {
        $helpArray = explode(',', $base64string);
        if (isset($helpArray[1])) {
            $base64string = $helpArray[1];
        } else {
            $base64string = $helpArray[0];
        }
        $data = base64_decode($base64string);
        if (base64_encode($data) !== $base64string) {
            throw new ValidationException('Invalid base64 format!');
        }
        $tmpPath = sys_get_temp_dir() . '/sf_upload' . uniqid();
        if (!file_put_contents($tmpPath, $data)) {
            throw new \Exception('Image not uploaded to temp directory!');
        }
        $file = new FileObject($tmpPath);
        if (!isset($this->acceptedMimeTypes[$file->getMimeType()])) {
            throw new ValidationException ('MIME type:' . $file->getMimeType() . ' not acceptable! Use jpeg,jpg or png.');
        }
        if (!($file->getSize() / CarAdService::KB_TO_MB) > CarAdService::MAX_IMAGE_SIZE) {
            throw new ValidationException ('Image size should not exceed' . CarAdService::MAX_IMAGE_SIZE . 'MB!');
        }

        return $file;
    }
}
