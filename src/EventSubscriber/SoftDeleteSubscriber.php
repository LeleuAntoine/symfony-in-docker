<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SoftDeleteSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $em = $args->getObjectManager();

        $date = new \DateTime();
        $comments = $entity->getComments();

        if ($comments) {
            foreach ($comments as $comment) {
                $comment->setDeletedAt($date);
            }
        }

        $entity->setDeletedAt($date);
        $em->persist($entity);
        $em->flush();

//        $args->;

    }
}