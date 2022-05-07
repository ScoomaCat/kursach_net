<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'masterAppointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $master;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date;

    #[ORM\Column(type: 'string', length: 10000, nullable: true)]
    private ?string $comment;

    #[ORM\Column(type: 'integer')]
    private ?int $cabinet;

    #[ORM\ManyToOne(targetEntity: AppointmentStatus::class)]
    private ?AppointmentStatus $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getMaster(): ?User
    {
        return $this->master;
    }

    public function setMaster(?User $master): self
    {
        $this->master = $master;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCabinet(): ?int
    {
        return $this->cabinet;
    }

    public function setCabinet(int $cabinet): self
    {
        $this->cabinet = $cabinet;

        return $this;
    }

    public function getStatus(): ?AppointmentStatus
    {
        return $this->status;
    }

    public function setStatus(?AppointmentStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    #[Pure] public function isConfirmed(): bool
    {
        return $this->getStatus() && $this->getStatus()->getId() === AppointmentStatus::STATUS_CONFIRMED_ID;
    }

    #[Pure] public function isUnconfirmed(): bool
    {
        return $this->getStatus() && $this->getStatus()->getId() === AppointmentStatus::STATUS_NOT_CONFIRMED_ID;
    }

    #[Pure] public function isCancelled(): bool
    {
        return $this->getStatus() && $this->getStatus()->getId() === AppointmentStatus::STATUS_CANCELLED_ID;
    }

    #[Pure] public function isCompleted(): bool
    {
        return $this->getStatus() && $this->getStatus()->getId() === AppointmentStatus::STATUS_COMPLETED_ID;
    }
}
