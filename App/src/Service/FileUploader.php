<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\User;
use App\Entity\Userfile;
use App\Util\FileInfo;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $entityManager;
    private $targetDirectory;
    private $slugger;

    // Better use a proxy for Slugger and ObjectManager, so this logic gets fully independent from Symfony and doctrine
    public function __construct(ObjectManager $entityManager, $targetDirectory, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * moves the file into a specified directory and creates a userfile entry in the database
     *
     * @param UploadedFile $file
     * @param User $user
     * @return string
     */
    public function upload(UploadedFile $file, User $user): string
    {
        try {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // use slugger because you should not trust user input
            $safeFilename = (string) $this->slugger->slug($fileName);
            $fileType = $file->guessExtension();

            $userFile = new Userfile();
            $userFile->setName($safeFilename);

            $userFile->setOwner($user);
            $userFile->setFiletype($fileType);
            $userFile->setPath($this->targetDirectory);
            $userFile->setFileSize($file->getSize());

            // persist userfile before actually saving the file, because we need the userfile id
            $this->entityManager->persist($userFile);
            $this->entityManager->flush();

            $file->move(
                $this->targetDirectory,
                FileInfo::getFullFilePath($userFile)
            );
        } catch (Exception $e) {
            // remove userfile if saving the actual file failed
            $this->entityManager->remove($userFile);
            $this->entityManager->flush();
            return "Uploading your file failed.";
        }

        return "Uploading your file succeeded.";
    }
}