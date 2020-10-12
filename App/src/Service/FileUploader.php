<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\User;
use App\Entity\Userfile;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $entityManager;
    private $targetDirectory;
    private $slugger;

    public function __construct(ObjectManager $entityManager, $targetDirectory, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, User $user): string
    {
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $this->slugger->slug($fileName);
        $fileType = $file->guessExtension();

        try {
            $userFile = new Userfile();
            $userFile->setName($safeFilename);

            $userFile->setOwner($user);
            $userFile->setFiletype($fileType);
            $userFile->setPath($this->targetDirectory);
            $userFile->setFileSize($file->getSize());

            $this->entityManager->persist($userFile);
            $this->entityManager->flush();

            $file->move(
                $this->targetDirectory,
                $safeFilename.'-'.$userFile->getId() . '.' . $fileType
            );

        } catch (Exception $e) {
            $this->entityManager->remove($userFile);
            $this->entityManager->flush();;
            return "Fehler beim Hochladen der Datei.";
        }

        return "Datei wurde erfolgreich hochgeladen";
    }
}