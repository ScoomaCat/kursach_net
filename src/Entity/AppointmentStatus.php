<?php

namespace App\Entity;

use App\Repository\AppointmentStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity()]
class AppointmentStatus
{
    public const STATUS_NOT_CONFIRMED_ID = 1;
    public const STATUS_CONFIRMED_ID = 2;
    public const STATUS_CANCELLED_ID = 3;
    public const STATUS_COMPLETED_ID = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    #[Pure] public function __toString(): string
    {
        return $this->getTitle();
    }
}
