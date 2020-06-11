<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Knojector\SteamAuthenticationBundle\User\AbstractSteamUser;
use Symfony\Component\Security\Core\Role\Role;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends AbstractSteamUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="takenBy")
     */
    private $tasks_taken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="addedBy")
     */
    private $tasks_added;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments_id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    public function __construct()
    {
        $this->roles = [];
        $this->tasks_taken = new ArrayCollection();
        $this->tasks_added = new ArrayCollection();
        $this->comments_id = new ArrayCollection();
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasksAdded(): Collection
    {
        return $this->tasks_added;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasksTaken(): Collection
    {
        return $this->tasks_taken;
    }


    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
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
            $commentsId->setUserId($this);
        }

        return $this;
    }

    public function removeCommentsId(Comment $commentsId): self
    {
        if ($this->comments_id->contains($commentsId)) {
            $this->comments_id->removeElement($commentsId);
            // set the owning side to null (unless already changed)
            if ($commentsId->getUserId() === $this) {
                $commentsId->setUserId(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}