<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Userfile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ObjectManager;
use App\Util\FileInfo;

class FileRenamer
{
    private $slugger;
    private $filesystem;
    private $entityManager;

    // Better use a proxy for Filesystem, EntityManager and ObjectManager, so this logic gets fully independent from Symfony and doctrine
    public function __construct(Filesystem $filesystem, ObjectManager $entityManager, SluggerInterface $slugger) {
        $this->filesystem = $filesystem;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * renames the file and updates the userfile entry in the database
     *
     * @param Userfile $userfile
     * @param string $newName
     * @return string
     */
    public function rename(Userfile $userfile, string $newName): string
    {
        try {
            // use slugger because you should not trust user input
            $safeFileName = (string) $this->slugger->slug($newName);

            if ($safeFileName === $userfile->getName()) return 'Renaming your file failed.';

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
            return 'Renaming your file failed.';
        }

        return 'Renaming your file succeeded.';
    }
}