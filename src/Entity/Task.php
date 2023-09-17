<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TimeEntry::class, orphanRemoval: true)]
    private Collection $timeEntries;

    public function __construct()
    {
        $this->timeEntries = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, TimeEntry>
     */
    public function getTimeEntries(): Collection
    {
        return $this->timeEntries;
    }

    public function addTimeEntry(TimeEntry $timeEntry): static
    {
        if (!$this->timeEntries->contains($timeEntry)) {
            $this->timeEntries->add($timeEntry);
            $timeEntry->setTask($this);
        }

        return $this;
    }

    public function removeTimeEntry(TimeEntry $timeEntry): static
    {
        if ($this->timeEntries->removeElement($timeEntry)) {
            // set the owning side to null (unless already changed)
            if ($timeEntry->getTask() === $this) {
                $timeEntry->setTask(null);
            }
        }

        return $this;
    }

    /**
     * Get the current TimeEntry associated with this Task.
     * This method assumes that the "current" TimeEntry is the last one in the collection.
     *
     * @return TimeEntry|null
     */
    public function getCurrentTimeEntry(): ?TimeEntry
    {
        $entries = $this->getTimeEntries();

        // Filter TimeEntries to find the one without an end time.
        $activeEntries = $entries->filter(function (TimeEntry $entry) {
            return $entry->getEndTime() === null;
        });

        if ($activeEntries->isEmpty()) {
            return null;
        }

        // Sort the active entries by id (descending).
        $sortedEntries = $activeEntries->toArray();
        usort($sortedEntries, function (TimeEntry $a, TimeEntry $b) {
            return $b->getId() <=> $a->getId();
        });

        // The first entry (if any) will be the "current" one.
        return reset($sortedEntries);
    }

    public function calculateTotalElapsedTime(): int
    {
        $totalElapsedTime = 0;
        foreach ($this->getTimeEntries() as $timeEntry) {
            if ($timeEntry->getEndTime() !== null) {
                $totalElapsedTime += $timeEntry->calculateElapsedTimeInSeconds();
            }
        }
        
        return $totalElapsedTime;
    }
}
