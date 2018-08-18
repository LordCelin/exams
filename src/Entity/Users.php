<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="mail", type="string", length=254, nullable=false)
     */
    private $mail;
    
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=false, unique=true)
     */
    private $username;
    
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=false, unique=true)
     */
    private $password;

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
    
    /**
     * @var int
     *
     * @ORM\Column(name="dpt_id", type="integer", nullable=false)
     */
    private $dptId;
    
    // WITH USERINTERFACE
    
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */    
    private $isActive;
        
    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }
    
    
//    public function getSalt()
//    {
//        // you *may* need a real salt depending on your encoder
//        // see section on salt below
//        return null;
//    }
    
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->userId,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->userId,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }
    
    // END WITH USERINTERFACE
    
    // WITH ADVANCEDUSERINTERFACE
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
    
    // END WITH ADVANCEDUSERINTERFACE

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
    
    public function getUsername() : ?string
    {
        return $this->username;
    }
    
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
    
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
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
    
    public function getDptId(): ?int
    {
        return $this->dptId;
    }

    public function setDptId(int $dptId): self
    {
        $this->dptId = $dptId;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }


}
