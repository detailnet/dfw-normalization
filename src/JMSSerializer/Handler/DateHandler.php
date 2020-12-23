<?php

declare(strict_types=1);

namespace Detail\Normalization\JMSSerializer\Handler;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Detail\Normalization\JMSSerializer\PhpDeserializationVisitor;
use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler as BaseDateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use function ucfirst;

class DateHandler extends BaseDateHandler
{
    /**
     * @return array
     */
    public static function getSubscribingMethods(): array
    {
        $methods = parent::getSubscribingMethods();
        $formats = ['php'];
        $types = ['DateTime', 'DateTimeImmutable', 'DateInterval'];

        foreach ($types as $type) {
            foreach ($formats as $format) {
                $methods[] = [
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                ];

                $methods[] = [
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serialize' . ucfirst($type),
                ];
            }
        }

        return $methods;
    }

    public function deserializeDateTimeFromPhp(
        PhpDeserializationVisitor $visitor,
        ?string $data,
        array $type
    ): ?DateTime {
        return $this->deserializeDateTimeFromJson(
            $this->createJsonDeserializationVisitor($visitor),
            $data,
            $type
        );
    }

    public function deserializeDateTimeImmutableFromPhp(
        PhpDeserializationVisitor $visitor,
        ?string $data,
        array $type
    ): ?DateTimeImmutable {
        return $this->deserializeDateTimeImmutableFromJson(
            $this->createJsonDeserializationVisitor($visitor),
            $data,
            $type
        );
    }

    public function deserializeDateIntervalFromPhp(
        PhpDeserializationVisitor $visitor,
        ?string $data,
        array $type
    ): ?DateInterval {
        return $this->deserializeDateIntervalFromJson(
            $this->createJsonDeserializationVisitor($visitor),
            $data,
            $type
        );
    }

    private function createJsonDeserializationVisitor(AbstractVisitor $visitor): JsonDeserializationVisitor
    {
        return new JsonDeserializationVisitor($visitor->getNamingStrategy());
    }
}
