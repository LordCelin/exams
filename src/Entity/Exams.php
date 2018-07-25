<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @var bool
     *
     * @ORM\Column(name="exam_status", type="smallint", nullable=false)
     */
    private $examStatus = '0';

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


}
