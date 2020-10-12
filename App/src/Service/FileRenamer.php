<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Userfile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ObjectManager;

class FileRenamer
{
    private $slugger;
    private $filesystem;
    private $entityManager;

    public function __construct(Filesystem $filesystem, ObjectManager $entityManager, SluggerInterface $slugger) {
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    public function rename(Userfile $userfile, string $newName): string
    {
        try {
            $safeFileName = (string) $this->slugger->slug($newName);

            $renamedUserFile = new Userfile();
            $renamedUserFile->setId($userfile->getId());
            $renamedUserFile->setName($safeFileName);
            $renamedUserFile->setFiletype($userfile->getFiletype());
            $renamedUserFile->setPath($userfile->getPath());

            $this->filesystem->rename(
                FileInfo::getFullFilePath($userfile),
                FileInfo::getFullFilePath($renamedUserFile)
            );

            $userfile->setName($safeFileName);

            $this->entityManager->persist($userfile);
            $this->entityManager->flush();

        } catch (Exception $e) {
            return 'Fehler beim Umbennen der Datei';
        }

        return 'Datei erfolgreic umbenannt';
    }
}