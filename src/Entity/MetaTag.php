<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaTagRepository")
 */
class MetaTag
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
    private $name;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PageMetaTags", inversedBy="metaTags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPage(): ?PageMetaTags
    {
        return $this->page;
    }

    public function setPage(?PageMetaTags $page): self
    {
        $this->page = $page;

        return $this;
    }
}
