<?php

namespace App\Entity;

use App\Repository\GameRepository;
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
    private int $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private string $resume;

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
     * @ORM\Column(type="date")
     * @Assert\Range(max="now")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Range(max="now")
     */
    private $modifiactionDate;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="game", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getResume(): string
    {
        return $this->resume;
    }

    /**
     * @param string $resume
     */
    public function setResume(string $resume): void
    {
        $this->resume = $resume;
    }

    /**
     * @return string|null
     */
    public function getMaterialRequired(): ?string
    {
        return $this->materialRequired;
    }

    /**
     * @param string|null $materialRequired
     */
    public function setMaterialRequired(?string $materialRequired): void
    {
        $this->materialRequired = $materialRequired;
    }

    /**
     * @return int
     */
    public function getDownload(): int
    {
        return $this->download;
    }

    /**
     * @param int $download
     */
    public function setDownload(int $download): void
    {
        $this->download = $download;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return Date
     */
    public function getCreationDate(): Date
    {
        return $this->creationDate;
    }

    /**
     * @param Date $creationDate
     */
    public function setCreationDate(Date $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return Date|null
     */
    public function getModifiactionDate(): ?Date
    {
        return $this->modifiactionDate;
    }

    /**
     * @param Date|null $modifiactionDate
     */
    public function setModifiactionDate(?Date $modifiactionDate): void
    {
        $this->modifiactionDate = $modifiactionDate;
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
}