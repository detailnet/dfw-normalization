<?php

declare(strict_types=1);

namespace Detail\Normalization\JMSSerializer\Handler;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\ArrayCollectionHandler as BaseArrayCollectionHandler;

class ArrayCollectionHandler extends BaseArrayCollectionHandler
{
    /**
     * @return array
     */
    public static function getSubscribingMethods(): array
    {
        $methods = parent::getSubscribingMethods();
        $formats = ['php'];
        $types = [
            'ArrayCollection',
            'Doctrine\Common\Collections\ArrayCollection',
            'Doctrine\ORM\PersistentCollection',
            'Doctrine\ODM\MongoDB\PersistentCollection',
            'Doctrine\ODM\PHPCR\PersistentCollection',
        ];

        foreach ($types as $type) {
            foreach ($formats as $format) {
                $methods[] = [
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serializeCollection',
                ];

                $methods[] = [
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'deserializeCollection',
                ];
            }
        }

        return $methods;
    }
}
