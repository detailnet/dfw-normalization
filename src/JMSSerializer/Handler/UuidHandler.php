<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use Ramsey\Uuid\Uuid;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;

use Detail\Normalization\Exception;

class UuidHandler implements
    SubscribingHandlerInterface
{
    const UUID_V4_PATTERN = '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}';

    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('php', 'json', 'xml', 'yml');

        foreach ($formats as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => 'uuid',
                'format' => $format,
                'method' => 'deserializeUuid',
            );

            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'uuid',
                'format' => $format,
                'method' => 'serializeUuid',
            );
        }

        return $methods;
    }

    public function deserializeUuid(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        if (null === $data) {
            return null;
        }

        if (!preg_match('/^' . self::UUID_V4_PATTERN . '$/', $data)) {
            throw new Exception\RuntimeException('Invalid UUID version 4 format');
        }

        $uuid = Uuid::fromString($data);

        return $uuid;
    }

    public function serializeUuid(VisitorInterface $visitor, Uuid $uuid, array $type, Context $context)
    {
        return $visitor->visitString($uuid->toString(), $type, $context);
    }
}
