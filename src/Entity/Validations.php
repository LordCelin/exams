<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Validations
 *
 * @ORM\Table(name="validations")
 * @ORM\Entity
 */
class Validations
{
    /**
     * @var int
     *
     * @ORM\Column(name="valid_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $validId;

    /**
     * @var int
     *
     * @ORM\Column(name="exam_id", type="integer", nullable=false)
     */
    private $examId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var bool
     *
     * @ORM\Column(name="valid_status", type="smallint", nullable=false)
     */
    private $validStatus = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=false)
     */
    private $comment;

    public function getValidId(): ?int
    {
        return $this->validId;
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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getValidStatus(): ?bool
    {
        return $this->validStatus;
    }

    public function setValidStatus(bool $validStatus): self
    {
        $this->validStatus = $validStatus;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }


}
