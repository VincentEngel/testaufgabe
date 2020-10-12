<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Userfile
 *
 * @ORM\Table(name="userfile", indexes={@ORM\Index(name="owner", columns={"owner"})})
 * @ORM\Entity
 */
class Userfile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=512, nullable=false)
     */
    private $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFiletype(): string
    {
        return $this->filetype;
    }

    /**
     * @param string $filetype
     */
    public function setFiletype(string $filetype): void
    {
        $this->filetype = $filetype;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=8, nullable=false)
     */
    private $filetype;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=128, nullable=false)
     */
    private $path;
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
     * })
     */
    private $owner;

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }


    /**
     * file size in bytes
     *
     * @var int
     *
     * @ORM\Column(name="file_size", type="integer", nullable=false)
     */
    private $fileSize;
}
