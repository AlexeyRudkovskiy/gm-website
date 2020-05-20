<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StaticPageRepository")
 */
class Settings implements TranslatableInterface
{

    use TranslatableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public $title;

    public $phone;

    public $fax;

    public $email;

    public $firstBlock;

    public $secondBlock;

    public $contactRequestsEmail;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Settings
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return Settings
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     * @return Settings
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
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
     * @return Settings
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstBlock()
    {
        return $this->firstBlock;
    }

    /**
     * @param mixed $firstBlock
     * @return Settings
     */
    public function setFirstBlock($firstBlock)
    {
        $this->firstBlock = $firstBlock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecondBlock()
    {
        return $this->secondBlock;
    }

    /**
     * @param mixed $secondBlock
     * @return Settings
     */
    public function setSecondBlock($secondBlock)
    {
        $this->secondBlock = $secondBlock;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContactRequestsEmail()
    {
        return $this->contactRequestsEmail;
    }

    /**
     * @param mixed $contactRequestsEmail
     * @return Settings
     */
    public function setContactRequestsEmail($contactRequestsEmail)
    {
        $this->contactRequestsEmail = $contactRequestsEmail;
        return $this;
    }


}
