<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function save(User $user): bool
    {
        $card = $this->checkCard($user);
        $isNew = !$user->getId();

        if (!$card){
            return $card;
        }

        if (method_exists($user, 'setPassword') && $user->getPlainPassword()) {
            $encodedPassword = $this->encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);
        }

        $this->em->persist($user);
        $this->em->flush();

        if ($isNew) {
            // Dispatcher un event
        }

        return true;
    }

    public function checkCard(User $user):bool
    {
        if ($user->getCard() !== null){
            return false;
        }
        return true;
    }
}