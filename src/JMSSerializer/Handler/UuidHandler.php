<?php

declare(strict_types=1);

namespace Detail\Normalization\JMSSerializer\Handler;

use Detail\Normalization\Exception;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function preg_match;

class UuidHandler implements
    SubscribingHandlerInterface
{
    public const UUID_V4_PATTERN = '[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}';

    /**
     * @return array
     */
    public static function getSubscribingMethods(): array
    {
        $methods = [];
        $formats = ['php', 'json', 'xml', 'yml'];

        foreach ($formats as $format) {
            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => 'uuid',
                'format' => $format,
                'method' => 'deserializeUuid',
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'uuid',
                'format' => $format,
                'method' => 'serializeUuid',
            ];
        }

        return $methods;
    }

    public function deserializeUuid(
        VisitorInterface $visitor,
        ?string $data,
        array $type,
        Context $context
    ): ?UuidInterface {
        if ($data === null) {
            return null;
        }

        if (!preg_match('/^' . self::UUID_V4_PATTERN . '$/', $data)) {
            throw new Exception\RuntimeException('Invalid UUID version 4 format');
        }

        return Uuid::fromString($data);
    }

    public function serializeUuid(VisitorInterface $visitor, Uuid $uuid, array $type, Context $context): ?string
    {
        return $visitor->visitString($uuid->toString(), $type, $context);
    }
}
