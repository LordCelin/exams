<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departments
 *
 * @ORM\Table(name="departments")
 * @ORM\Entity
 */
class Departments
{
    /**
     * @var int
     *
     * @ORM\Column(name="dpt_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $dptId;

    /**
     * @var string
     * 
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="dpt_name", type="string", length=32, nullable=false)
     */
    private $dptName;

    public function getDptId(): ?int
    {
        return $this->dptId;
    }

    public function getDptName(): ?string
    {
        return $this->dptName;
    }

    public function setDptName(string $dptName): self
    {
        $this->dptName = $dptName;

        return $this;
    }


}
