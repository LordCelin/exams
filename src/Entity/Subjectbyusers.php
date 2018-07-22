<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subjectbyusers
 *
 * @ORM\Table(name="subjectbyusers")
 * @ORM\Entity
 */
class Subjectbyusers
{
    /**
     * @var int
     *
     * @ORM\Column(name="subj_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $subjId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    public function getSubjId(): ?int
    {
        return $this->subjId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }


}
