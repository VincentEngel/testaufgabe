<?php

declare(strict_types = 1);

namespace App\Service;


use App\Entity\Userfile;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Persistence\ObjectManager;
use App\Util\FileInfo;

class FileRemover
{
    private $filesystem;
    private $entityManager;

    // Better use a proxy for Filesystem and ObjectManager, so this logic gets fully independent from Symfony and doctrine
    public function __construct(Filesystem $filesystem, ObjectManager $entityManager) {
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }

    /**
     * Deletes the file and removes the userfile entry in the database
     *
     * @param Userfile $userfile
     * @return string
     */
    public function delete(Userfile $userfile): string
    {
        try {
            $this->filesystem->remove(FileInfo::getFullFilePath($userfile));
            $this->entityManager->remove($userfile);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return 'Deleting your file failed.';
        }
        return 'Deleting your file succeeded.';
    }
}