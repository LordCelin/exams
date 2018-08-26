<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Users;

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
     * @ORM\Column(name="title", type="string", length=32, nullable=true)
     */
    private $title;
    
    /**
     * @var string
     * 
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="subject", type="string", length=64, nullable=true)
     */
    private $subject;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
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
     * @ORM\Column(name="date_of_submit", type="datetime", nullable=true)
     */
    private $dateOfSubmit;

    /**
     * @var \Date
     *
     * @ORM\Column(name="deadline", type="date", nullable=false)
     * 
     * @Assert\Range( min = "tomorrow", minMessage= "You must choose at least tomorrow")
     */
    private $deadline;
    
    // EXAM_STATUS: 0 = ASKED BY SECRETARY MEMBER, 1 = SUBMITTED, 2 = VALIDATED BY INTERNS, 3 = VALIDATED BY EXTERNS

    /**
     * @var int
     *
     * @ORM\Column(name="exam_status", type="integer", nullable=false)
     */
    private $examStatus = '0';
    
    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=64, nullable=true)
     */
    private $fileName;
    
    /**
     * @Assert\File(maxSize="5M")
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
    
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getExamStatus(): ?int
    {
        return $this->examStatus;
    }

    public function setExamStatus(int $examStatus): self
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
