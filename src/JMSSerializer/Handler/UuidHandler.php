<?php

declare(strict_types=1);

namespace Detail\Normalization\JMSSerializer\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function sprintf;

class UuidHandler implements
    SubscribingHandlerInterface
{
    private const TYPE = 'uuid';

    /**
     * @return array
     */
    public static function getSubscribingMethods(): array
    {
        $methods = [];
        $formats = ['json', 'xml', 'yml'];

        foreach ($formats as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'type' => self::TYPE,
                'format' => $format,
                'method' => 'deserializeUuid',
            ];

            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => self::TYPE,
                'format' => $format,
                'method' => 'serializeUuid',
            ];
        }

        return $methods;
    }

    public function deserializeUuid(
        DeserializationVisitorInterface $visitor,
        ?string $uuid,
        array $type,
        SerializationContext $context
    ): ?UuidInterface {
        if ($uuid === null) {
            return null;
        }

        if (!Uuid::isValid($uuid)) {
            throw new InvalidUuidStringException(sprintf('Invalid UUID "%s" given', $uuid));
        }

        return Uuid::fromString($uuid);
    }

    public function serializeUuid(
        SerializationVisitorInterface $visitor,
        UuidInterface $uuid,
        array $type,
        DeserializationContext $context
    ): ?string {
        return $visitor->visitString($uuid->toString(), $type);
    }
}
