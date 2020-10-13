<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\User;
use App\Entity\Userfile;
use App\Util\FileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;

    // Better use a proxy for Slugger and UploadFile so this logic gets fully independent from Symfony and doctrine
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function saveFile(UploadedFile $file, Userfile $userfile): bool {
        try {
            $file->move(
                $userfile->getPath(),
                FileInfo::getFullFilePath($userfile)
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createUserfile(UploadedFile $file, User $user, string $targetDirectory): Userfile {
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // use slugger because you should not trust user input, however only the same user gets to download this file,
        // maybe keeping the original name is fine?
        $safeFilename = (string) $this->slugger->slug($fileName);
        $fileType = $file->guessExtension();

        $userFile = new Userfile();
        $userFile->setName($safeFilename);

        $userFile->setOwner($user);
        $userFile->setFiletype($fileType);
        $userFile->setPath($targetDirectory);
        $userFile->setFileSize($file->getSize());

        return $userFile;
    }
}