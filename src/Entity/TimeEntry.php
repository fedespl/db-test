<?php

namespace App\Entity;

use App\Repository\TimeEntryRepository;
use App\Validator\Constraints as CustomAssert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'timeEntries')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Task $task = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[CustomAssert\EndTimeGreaterThanStartTime]
    private ?\DateTimeInterface $endTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function calculateElapsedTimeInSeconds(): ?int
    {
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();

        if ($startTime && $endTime) {
            return $endTime->getTimestamp() - $startTime->getTimestamp();
        }

        return null;
    }

    /**
     * Calculate the total elapsed time and return it in HH:MM:SS format.
     *
     * @return string|null
     */
    public function getTotalElapsedTime(): ?string
    {
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();

        if ($startTime && $endTime) {
            $elapsed = $endTime->diff($startTime);

            // Format the elapsed time as HH:MM:SS
            return $elapsed->format('%H:%I:%S');
        }

        return null;
    }
}
