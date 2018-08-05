<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Exams
 *
 * @ORM\Table(name="exams")
 * @ORM\Entity
 */
class Exams
{
    /**
     * @var int
     *
     * @ORM\Column(name="exam_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examId;

    /**
     * @var int
     *
     * @ORM\Column(name="secr_user_id", type="integer", nullable=false)
     */
    private $secrUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=32)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_submit", type="datetime")
     */
    private $dateOfSubmit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="intern_dl", type="datetime")
     */
    private $internDl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="extern_dl", type="datetime", nullable=false)
     */
    private $externDl;
    
    //EXAM_STATUS : 0 = ASKED BY SECRETARY MEMBER, 1 = SUBMITTED, 2 = VALIDATED BY INTERNS, 3 = VALIDATED BY EXTERNS

    /**
     * @var bool
     *
     * @ORM\Column(name="exam_status", type="smallint", nullable=false)
     */
    private $examStatus = '0';
    
     /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=64, nullable=false)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=64, nullable=false)
     */
    private $filePath;
    
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function getExamId(): ?int
    {
        return $this->examId;
    }

    public function getSecrUserId(): ?int
    {
        return $this->secrUserId;
    }

    public function setSecrUserId(int $secrUserId): self
    {
        $this->secrUserId = $secrUserId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateOfSubmit(): ?\DateTimeInterface
    {
        return $this->dateOfSubmit;
    }

    public function setDateOfSubmit(\DateTimeInterface $dateOfSubmit): self
    {
        $this->dateOfSubmit = $dateOfSubmit;

        return $this;
    }

    public function getInternDl(): ?\DateTimeInterface
    {
        return $this->internDl;
    }

    public function setInternDl(\DateTimeInterface $internDl): self
    {
        $this->internDl = $internDl;

        return $this;
    }

    public function getExternDl(): ?\DateTimeInterface
    {
        return $this->externDl;
    }

    public function setExternDl(\DateTimeInterface $externDl): self
    {
        $this->externDl = $externDl;

        return $this;
    }

    public function getExamStatus(): ?bool
    {
        return $this->examStatus;
    }

    public function setExamStatus(bool $examStatus): self
    {
        $this->examStatus = $examStatus;

        return $this;
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }


}
