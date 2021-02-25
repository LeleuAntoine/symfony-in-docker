<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @Vich\Uploadable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Game
{
    /*
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    use SoftDeleteableEntity;

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
    private ?string $picture;

    /**
     * @Vich\UploadableField(mapping="game_image", fileNameProperty="picture")
     *
     * @var File
     */
    private $pictureFile;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="game", orphanRemoval=true)
     */
    private Collection $comments;

    /**
     * @ORM\Column(type="datetime")
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

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getMaterialRequired(): ?string
    {
        return $this->materialRequired;
    }

    public function setMaterialRequired(?string $materialRequired): self
    {
        $this->materialRequired = $materialRequired;

        return $this;
    }

    public function getDownload(): int
    {
        return $this->download;
    }

    public function setDownload(int $download): self
    {
        $this->download = $download;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(?File $pictureFile = null)
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            $this->modificationDate = new \DateTime('now');
        }
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
