<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Files
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class Files
{
    /**
     * @var int
     *
     * @ORM\Column(name="file_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $fileId;

    /**
     * @var int
     *
     * @ORM\Column(name="exam_id", type="integer", nullable=false)
     */
    private $examId;

    /**
     * @var bool
     *
     * @ORM\Column(name="exam_status", type="boolean", nullable=false)
     */
    private $examStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="file_link", type="string", length=64, nullable=false)
     */
    private $fileLink;

    public function getFileId(): ?int
    {
        return $this->fileId;
    }

    public function getExamId(): ?int
    {
        return $this->examId;
    }

    public function setExamId(int $examId): self
    {
        $this->examId = $examId;

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

    public function getFileLink(): ?string
    {
        return $this->fileLink;
    }

    public function setFileLink(string $fileLink): self
    {
        $this->fileLink = $fileLink;

        return $this;
    }


}
