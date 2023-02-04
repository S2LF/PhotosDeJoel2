<?php

namespace App\Entity;

use App\Repository\BaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BaseRepository::class)]
class Base
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $headerContent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $homepageWord = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $homepageImagePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textFooter = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\Column]
    private ?bool $is_random_image = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteTitle(): ?string
    {
        return $this->siteTitle;
    }

    public function setSiteTitle(?string $siteTitle): self
    {
        $this->siteTitle = $siteTitle;

        return $this;
    }

    public function getHeaderContent(): ?string
    {
        return $this->headerContent;
    }

    public function setHeaderContent(?string $headerContent): self
    {
        $this->headerContent = $headerContent;

        return $this;
    }

    public function getHomepageWord(): ?string
    {
        return $this->homepageWord;
    }

    public function setHomepageWord(?string $homepageWord): self
    {
        $this->homepageWord = $homepageWord;

        return $this;
    }

    public function getHomepageImagePath(): ?string
    {
        return $this->homepageImagePath;
    }

    public function setHomepageImagePath(string|null $homepageImagePath): self
    {
        $this->homepageImagePath = $homepageImagePath;

        return $this;
    }

    public function getTextFooter(): ?string
    {
        return $this->textFooter;
    }

    public function setTextFooter(?string $textFooter): self
    {
        $this->textFooter = $textFooter;

        return $this;
    }

    /**
     * Get the value of deletedAt
     *
     * @return ?\DateTimeInterface
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * Set the value of deletedAt
     *
     * @param ?\DateTimeInterface $deletedAt
     *
     * @return self
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isIsRandomImage(): ?bool
    {
        return $this->is_random_image;
    }

    public function setIsRandomImage(bool $is_random_image = false): self
    {
        $this->is_random_image = $is_random_image;

        return $this;
    }
}
