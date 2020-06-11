<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    const PR_HIGH = "Wysoki";
    const PR_MEDIUM = "Średni";
    const PR_LOW = "Niski";

    const ST_WAITING = "Oczekuje na podjęcie działań";
    const ST_TOOK = "W trakcie działań";
    const ST_FINISHED = "Działania zakończone";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks_added")
     */
    private $addedBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks_taken")
     */
    private $takenBy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priority;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $description;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deadline;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="task", orphanRemoval=true)
     */
    private $comments_id;

    public function __construct()
    {
        $this->comments_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(User $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }


    public function getTakenBy(): ?User
    {
        return $this->takenBy;
    }

    public function setTakenBy(?User $takenBy): self
    {
        $this->takenBy = $takenBy;

        return $this;
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

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getCommentsId(): Collection
    {
        return $this->comments_id;
    }

    public function addCommentsId(Comment $commentsId): self
    {
        if (!$this->comments_id->contains($commentsId)) {
            $this->comments_id[] = $commentsId;
            $commentsId->setTaskId($this);
        }

        return $this;
    }

    public function removeCommentsId(Comment $commentsId): self
    {
        if ($this->comments_id->contains($commentsId)) {
            $this->comments_id->removeElement($commentsId);
            // set the owning side to null (unless already changed)
            if ($commentsId->getTaskId() === $this) {
                $commentsId->setTaskId(null);
            }
        }

        return $this;
    }
}