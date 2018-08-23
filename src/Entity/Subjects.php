<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subjects
 *
 * @ORM\Table(name="subjects")
 * @ORM\Entity
 */
class Subjects
{
    /**
     * @var int
     *
     * @ORM\Column(name="subj_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $subjId;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="subj_name", type="string", length=32, nullable=false)
     */
    private $subjName;

    /**
     * @var int
     *
     * @ORM\Column(name="dpt_id", type="integer", nullable=false)
     */
    private $dptId;

    public function getSubjId(): ?int
    {
        return $this->subjId;
    }

    public function getSubjName(): ?string
    {
        return $this->subjName;
    }

    public function setSubjName(string $subjName): self
    {
        $this->subjName = $subjName;

        return $this;
    }

    public function getDptId(): ?int
    {
        return $this->dptId;
    }

    public function setDptId(int $dptId): self
    {
        $this->dptId = $dptId;

        return $this;
    }


}
