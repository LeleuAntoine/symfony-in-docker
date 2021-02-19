<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Card
{
    /**
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
     * @Assert\NotBlank
     */
    private $numbercard;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Date
     * @var string A "m-Y" formatted value
     * @Assert\NotBlank
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $visualCryptogram;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="card", cascade={"persist", "remove"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumbercard(): ?string
    {
        return $this->numbercard;
    }

    public function setNumbercard(string $numbercard): self
    {
        $this->numbercard = $numbercard;

        return $this;
    }

    public function getExpirationDate(): string
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(string $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getVisualCryptogram(): ?int
    {
        return $this->visualCryptogram;
    }

    public function setVisualCryptogram(int $visualCryptogram): self
    {
        $this->visualCryptogram = $visualCryptogram;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getCard() !== $this) {
            $user->setCard($this);
        }

        $this->user = $user;

        return $this;
    }
}
