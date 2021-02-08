<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private ?string $resume;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $materialRequired;

    /**
     * @ORM\Column(type="integer")
     */
    private int $download;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $photo;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="game", orphanRemoval=true)
     */
    private Collection $comments;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private ?DateTimeInterface $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $modificationDate;

    public function __construct()
    {
        $this->download = 0;
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Game
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Game
    {
        $this->name = $name;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): Game
    {
        $this->resume = $resume;

        return $this;
    }

    public function getMaterialRequired(): ?string
    {
        return $this->materialRequired;
    }

    public function setMaterialRequired(?string $materialRequired): Game
    {
        $this->materialRequired = $materialRequired;

        return $this;
    }

    public function getDownload(): int
    {
        return $this->download;
    }

    public function setDownload(int $download): Game
    {
        $this->download = $download;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): Game
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setGame($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGame() === $this) {
                $comment->setGame(null);
            }
        }

        return $this;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getModificationDate(): ?DateTimeInterface
    {
        return $this->modificationDate;
    }

    public function setModificationDate(?DateTimeInterface $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }
}
