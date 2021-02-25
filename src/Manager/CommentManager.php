<?php

namespace App\Manager;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CommentManager
{
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function save(Comment $comment): Comment
    {
        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    public function delete(Comment $comment): Comment
    {
        $this->em->remove($comment);
        $this->em->flush();

        return $comment;
    }
}
