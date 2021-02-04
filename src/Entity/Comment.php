<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
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
    private string $title;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private string $content;

    /**
     * @ORM\Column(type="date")
     * @Assert\Range(
     *     max = "now")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Range(
     *     max = "now")
     */
    private $modifiactionDate;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

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

    public function getModifiactionDate(): Date
    {
        return $this->modifiactionDate;
    }

    /**
     * @param Date $modifiactionDate
     */
    public function setModifiactionDate(Date $modifiactionDate): void
    {
        $this->modifiactionDate = $modifiactionDate;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
