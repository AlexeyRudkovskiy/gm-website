<?php


namespace App\Traits;


use App\Services\PhotosService;
use App\Services\UploadedPhoto;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

trait UploadFile
{


    /** todo: move this code to service */
    /**
     * @param $object
     * @param string $field
     * @param FormInterface $form
     * @param PhotosService $photosService
     * @return array
     */
    public function uploadOnePhoto($object, string $field, FormInterface $form, PhotosService $photosService): array
    {
        /** @var UploadedFile $previewFile */
        $previewFile = $form[$field]->getData();

        if ($previewFile !== null) {
            $originalsDirectory = $this->getParameter('originals_directory');
            $directory = $this->getParameter('previews_directory');

            $originalFilename = $this->saveOriginal($previewFile, $originalsDirectory);

            /// todo: need to delete photos when uploading one photo
            $_photos = $photosService->processPhoto($previewFile, $originalsDirectory . '/' . $originalFilename, $directory);
            $photos = [];
//            $photo = array_pop($photo);

            /** @var UploadedPhoto $photo */
            foreach ($_photos as $photo) {
                $photos[$photo->getSize()] = [
                    'directory' => $photo->getDirectory(),
                    'filename' => $photo->getFilename()
                ];
            }

            $response = [
                'files' => $photos,
                'original' => [
                    'directory' => $originalsDirectory,
                    'filename' => $originalFilename
                ]
            ];

            call_user_func([$object, $this->getMethodName($field, true)], $response);
            return $response;
        }

        return [];
    }

    /**
     * @param $object
     * @param string $fieldName
     * @param FormInterface $form
     * @param PhotosService $photosService
     * @return array
     */
    public function uploadMultiplePhotos($object, string $fieldName, FormInterface $form, PhotosService $photosService): array
    {
        $files = $form[$fieldName]->getData();
        $originalsDirectory = $this->getParameter('originals_directory');
        $directory = $this->getParameter('previews_directory');

        $uploadedPhotos = [];

        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $originalFilename = $this->saveOriginal($file, $originalsDirectory);

            $_photos = $photosService->processPhoto($file, $originalsDirectory . '/' . $originalFilename, $directory);
            $photos = [];
            /** @var UploadedPhoto $photo */
            foreach ($_photos as $photo) {
                $photos[$photo->getSize()] = [
                    'directory' => $photo->getDirectory(),
                    'filename' => $photo->getFilename()
                ];
            }

//            $photos['@original'] = $originalsDirectory . '/' . $originalFilename;
            array_push($uploadedPhotos, [
                'files' => $photos,
                'original' => [
                    'directory' => $originalsDirectory,
                    'filename' => $originalFilename
                ]
            ]);
        }

        call_user_func([$object, $this->getMethodName($fieldName, true)], $uploadedPhotos);
        return $uploadedPhotos;
    }

    public function regeneratePreviews(array $photos, Request $request, PhotosService $photosService)
    {
        $index = $request->query->getInt('index');
        $targetPhoto = $photos[$index];

        $updatePhoto = $this->regenerateOne($targetPhoto, $photosService);
        $photos[$index] = $updatePhoto;

        return $photos;
    }

    public function regenerateOne(array $photo, PhotosService $photosService)
    {
        $original = $photo['original'];
        $originalPath = $original['directory'] . '/' . $original['filename'];
        $originalImage = new UploadedFile($originalPath, $original['filename']);

        $this->deleteGeneratedPhotosFromDisk($photo);
        $target = $this->getParameter('previews_directory');

        $newPhotos = $photosService->processPhoto($originalImage, $originalPath, $target);
        $_newPhotos = [];

        foreach ($newPhotos as $_photo) {
            $_newPhotos[$_photo->getSize()] = [
                'directory' => $_photo->getDirectory(),
                'filename' => $_photo->getFilename()
            ];
        }

        $photo['files'] = $_newPhotos;
        return $photo;
    }

    public function processRegenerateOneAndUpdate($entity, $fieldName, PhotosService $photosService)
    {
        $setterName = $this->getMethodName($fieldName, true);
        $getterName = $this->getMethodName($fieldName, false);

        $currentValue = call_user_func([ $entity, $getterName ]);
        $images = $this->regenerateOne($currentValue, $photosService);
        call_user_func([ $entity, $setterName ], $images);

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        $manager->persist($entity);
        $manager->flush();

        $files = $images['files'];
        $baseUrl = $this->getParameter('preview_directory_url');

        foreach ($files as $key => $file) {
            $files[$key] = $baseUrl . '/' . $file['filename'];
        }

        return $this->json([
            'image' => $files
        ]);
    }

    /**
     * @param array $photo
     */
    public function deletePhotoFromDisk(array $photo)
    {
        $original = $photo['original'];
        unlink($original['directory'] . '/' . $original['filename']);

        $this->deleteGeneratedPhotosFromDisk($photo);
    }

    public function processDeleteOneAndUpdate($entity, string $fieldName)
    {
        $setterName = $this->getMethodName($fieldName, true);
        $getterName = $this->getMethodName($fieldName, false);

        $currentValue = call_user_func([ $entity, $getterName ]);
        $this->deletePhotoFromDisk($currentValue);
        call_user_func([ $entity, $setterName ], []);

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        $manager->persist($entity);
        $manager->flush();

        return $this->json([
            'status' => 'success'
        ]);
    }

    public function deleteGeneratedPhotosFromDisk(array $photo)
    {
        $files = $photo['files'];

        foreach ($files as $file) {
            $filePath = $file['directory'] . '/' . $file['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * @param $object
     */
    private function removeImageIfExists($object) {
        $photoFilename = $this->getCurrentPhotos($object);
        $directory = $this->getParameter('previews_directory');
        $absoluteFilePath = $directory . '/' . $photoFilename;

        if (is_file($absoluteFilePath)) {
            unlink($absoluteFilePath);
        }
    }

    /**
     * @param UploadedFile $file
     * @param string $originalsDirectory
     * @return string
     */
    private function saveOriginal(UploadedFile $file, string $originalsDirectory): string {
        $originalFilename = sha1(implode('@', [ $this->getEntityTypeIdentifier(), $file->getClientOriginalName(), time() ]));
        $originalFilename .= '-' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($originalsDirectory, $originalFilename);

        return $originalFilename;
    }

    protected function getMethodName(string $field, bool $isSetter): string
    {
        $prefix = $isSetter ? 'set' : 'get';
        $field = ucfirst($field);

        return $prefix . $field;
    }

}
