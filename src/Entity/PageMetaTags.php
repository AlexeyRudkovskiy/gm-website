<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageMetaTagsRepository")
 */
class PageMetaTags
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MetaTag", mappedBy="page", orphanRemoval=true)
     */
    private $metaTags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title = '';

    public function __construct()
    {
        $this->metaTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|MetaTag[]
     */
    public function getMetaTags(): Collection
    {
        return $this->metaTags;
    }

    public function addMetaTag(MetaTag $metaTag): self
    {
        if (!$this->metaTags->contains($metaTag)) {
            $this->metaTags[] = $metaTag;
            $metaTag->setPage($this);
        }

        return $this;
    }

    public function removeMetaTag(MetaTag $metaTag): self
    {
        if ($this->metaTags->contains($metaTag)) {
            $this->metaTags->removeElement($metaTag);
            // set the owning side to null (unless already changed)
            if ($metaTag->getPage() === $this) {
                $metaTag->setPage(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

}
