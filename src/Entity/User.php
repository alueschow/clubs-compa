<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
* @ORM\Table(name="User")
* @ORM\Entity
* @UniqueEntity(fields="email", message="Email already taken")
* @UniqueEntity(fields="username", message="Username already taken")
*/
class User implements UserInterface, \Serializable
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="string", length=25, unique=true)
    */
    private $username;

    /**
    * @ORM\Column(type="string", length=64)
    */
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
    * @ORM\Column(type="string", length=254, unique=true, nullable=true)
    */
    private $email;

    /**
    * @ORM\Column(name="is_active", type="boolean")
    */
    private $isActive;

    /**
     * @ORM\Column(type="array")
     */
    private $groups;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;



    public function __construct()
    {
        $this->isActive = true;
        // not needed when using bcrypt
        // $this->salt = md5(uniqid('', true));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }


    /**
     * @return mixed
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // using bcrypt (see security.yml) does not require a salt, so return null
        return null;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @@param $roles
     */
    public function setRoles($roles)
    {
        $this->roles = array($roles);
    }


    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @@param $groups
     */
    public function setGroups($groups)
    {
        if (is_array($groups)) {
            foreach ($groups as $group) {
                $this->groups[] = $group;
            }
        } else {
            $this->groups[] = $groups;
        }
    }

    public function eraseCredentials()
    {
    }


    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->groups,
            $this->roles
            // salt not needed when using bcrypt
            //// $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->groups,
            $this->roles
            // salt not needed when using bcrypt
            //// $this->salt
        ) = unserialize($serialized, array('allowed_classes' => true));
    }
}