<?php


namespace App\Entity;


use App\Traits\PhotosByName;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;


/**
 * @ORM\Entity
 */
class Product implements TranslatableInterface
{

    use TranslatableTrait;
    use PhotosByName;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $image = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showInFooter = false;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $orderIndex = 1;

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
    public function getImage(): ?array
    {
        if (gettype($this->image) === 'string') {
            return [];
        }
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->translate()->getTitle();
    }

    public function getDescription(): string
    {
        return $this->translate()->getDescription();
    }

    public function getContent(): string
    {
        return $this->translate()->getContent();
    }

    /**
     * @return mixed
     */
    public function getShowInFooter()
    {
        return $this->showInFooter;
    }

    /**
     * @param mixed $showInFooter
     * @return Product
     */
    public function setShowInFooter($showInFooter)
    {
        $this->showInFooter = $showInFooter;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderIndex(): int
    {
        return $this->orderIndex ?? 1;
    }

    /**
     * @param int $orderIndex
     * @return Product
     */
    public function setOrderIndex(int $orderIndex): Product
    {
        $this->orderIndex = $orderIndex;
        return $this;
    }



}
