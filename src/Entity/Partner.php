<?php

namespace App\Entity;

use App\Traits\PhotosByName;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartnerRepository")
 */
class Partner implements TranslatableInterface
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
     * @var int
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $orderIndex = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(array $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->translate()->getTitle();
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
     * @return Partner
     */
    public function setOrderIndex(int $orderIndex): Partner
    {
        $this->orderIndex = $orderIndex;
        return $this;
    }



}
