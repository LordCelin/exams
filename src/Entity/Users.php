<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=32, nullable=false)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=false, options={"default"="123"})
     */
    private $password = '123';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=32, nullable=false)
     */
    private $firstname;

    /**
     * @var bool
     *
     * @ORM\Column(name="hod", type="boolean", nullable=false)
     */
    private $hod;

    /**
     * @var bool
     *
     * @ORM\Column(name="secretary_member", type="boolean", nullable=false)
     */
    private $secretaryMember;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getHod(): ?bool
    {
        return $this->hod;
    }

    public function setHod(bool $hod): self
    {
        $this->hod = $hod;

        return $this;
    }

    public function getSecretaryMember(): ?bool
    {
        return $this->secretaryMember;
    }

    public function setSecretaryMember(bool $secretaryMember): self
    {
        $this->secretaryMember = $secretaryMember;

        return $this;
    }


}
