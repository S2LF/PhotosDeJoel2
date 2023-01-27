<?php

namespace App\Service;

use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, $newFileName, $directory, $exif = null)
    {
        // $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $newFileName);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory() . $directory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            $this->addFlash("error", "Une problème est survenu lors de l'upload de l'image");
        }
        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

      /**
       * Recursively deletes a directory tree.
       *
       * @param string $folder         The directory path.
       * @param bool   $keepRootFolder Whether to keep the top-level folder.
       *
       * @return bool TRUE on success, otherwise FALSE.
       */
      public function deleteTree(
          $folder,
          $keepRootFolder = false
      )
      {
          // Handle bad arguments.
          if (empty($folder) || !file_exists($folder)) {
              return true; // No such file/folder exists.
          } elseif (is_file($folder) || is_link($folder)) {
              return @unlink($folder); // Delete file/link.
          }

          // Delete all children.
          $files = new \RecursiveIteratorIterator(
              new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
              \RecursiveIteratorIterator::CHILD_FIRST
          );

          foreach ($files as $fileinfo) {
              $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
              if (!@$action($fileinfo->getRealPath())) {
                  return false; // Abort due to the failure.
              }
          }

          // Delete the root folder itself?
          return (!$keepRootFolder ? @rmdir($folder) : true);
      }

    /**
       * deleteFile
       *
       * @param  mixed $path
       * @param  mixed $isPublic
       * @return void
       */
    public function deleteFile(string $path)
    {
        $fileSystem = new Filesystem();

        $result = $fileSystem->remove($path);
        if ($result === false) {
            throw new \Exception(sprintf('Erreur en supprimant "%s"', $path));
        }
    }
}
