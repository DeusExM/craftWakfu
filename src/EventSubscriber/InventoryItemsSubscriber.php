<?php

namespace App\EventSubscriber;

use App\Entity\InventoryItems;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class InventoryItemsSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
    }
}
