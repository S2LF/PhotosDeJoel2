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

    public function setHomepageWord(string $homepageWord): self
    {
        $this->homepageWord = $homepageWord;

        return $this;
    }

    public function getHomepageImagePath(): ?string
    {
        return $this->homepageImagePath;
    }

    public function setHomepageImagePath(string $homepageImagePath): self
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
}
