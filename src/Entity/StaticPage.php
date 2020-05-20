<?php

namespace App\Entity;

use App\Traits\PhotosByName;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StaticPageRepository")
 */
class StaticPage implements TranslatableInterface
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
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="json")
     */
    private $photos = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showInFooter = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->translate()->getTitle();
    }

    public function getContent(): string
    {
        return $this->translate()->getContent();
    }

    public function getPhotos(): ?array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    public function getFirstPhoto()
    {
        return $this->getPhotos()[0] ?? null;
    }

    public function getAllOtherPhotos()
    {
        $photos = $this->getPhotos();
        return array_splice($photos, 1);
    }

    public function getSmallContent()
    {
        $content = explode(PHP_EOL, $this->getContent());
        $content = array_splice($content, 0, 4);
        return implode(PHP_EOL, $content);
    }

    public function getShowInFooter(): ?bool
    {
        return $this->showInFooter;
    }

    public function setShowInFooter(bool $showInFooter): self
    {
        $this->showInFooter = $showInFooter;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

}
