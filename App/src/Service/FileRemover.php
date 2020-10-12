<?php

declare(strict_types = 1);

namespace App\Service;


use App\Entity\Userfile;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Persistence\ObjectManager;

class FileRemover
{
    private $filesystem;
    private $entityManager;

    public function __construct(Filesystem $filesystem, ObjectManager $entityManager) {
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
    }

    public function delete(Userfile $userfile): string
    {
        try {
            $this->filesystem->remove(FileInfo::getFullFilePath($userfile));
            $this->entityManager->remove($userfile);
            $this->entityManager->flush();
        } catch (Exception $e) {
            return 'Fehler beim Löschen der Datei';
        }
        return 'Datei erfolgreich gelöscht';
    }
}