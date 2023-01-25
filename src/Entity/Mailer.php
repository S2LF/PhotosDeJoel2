<?php

namespace App\Entity;

use App\Repository\MailerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MailerRepository::class)]
class Mailer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NoReplyEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $adminEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $rgpdText = null;

    #[ORM\Column(length: 255)]
    private ?string $emailSubject = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoReplyEmail(): ?string
    {
        return $this->NoReplyEmail;
    }

    public function setNoReplyEmail(string $NoReplyEmail): self
    {
        $this->NoReplyEmail = $NoReplyEmail;

        return $this;
    }

    public function getAdminEmail(): ?string
    {
        return $this->adminEmail;
    }

    public function setAdminEmail(string $adminEmail): self
    {
        $this->adminEmail = $adminEmail;

        return $this;
    }

    public function getRgpdText(): ?string
    {
        return $this->rgpdText;
    }

    public function setRgpdText(string $rgpdText): self
    {
        $this->rgpdText = $rgpdText;

        return $this;
    }

    public function getEmailSubject(): ?string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(string $emailSubject): self
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }
}
