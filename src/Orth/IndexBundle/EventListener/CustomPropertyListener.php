<?php

namespace Orth\IndexBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\ElasticaBundle\Event\TransformEvent;

class CustomPropertyListener implements EventSubscriberInterface
{
    private $anotherService;

    // ...

    public function addCustomProperty(TransformEvent $event)
    {
        $document = $event->getDocument();
        $custom = $this->anotherService->calculateCustom($event->getObject());

        $document->set('custom', $custom);
    }

    public static function getSubscribedEvents()
    {
        return array(
            TransformEvent::POST_TRANSFORM => 'addCustomProperty',
        );
    }
}