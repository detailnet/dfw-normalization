<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler as BaseDateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;

use Detail\Normalization\Exception;
use Detail\Normalization\JMSSerializer\PhpDeserializationVisitor;

class DateImmutableHandler extends BaseDateHandler
{
    use DatePhpDeserializationTrait;

    /**
     * @param string $defaultFormat
     * @param string $defaultTimezone
     */
    public function __construct($defaultFormat = DateTime::ISO8601, $defaultTimezone = 'UTC')
    {
        $this->format = $defaultFormat;
        $this->timezone = new DateTimeZone($defaultTimezone);

        parent::__construct($defaultFormat, $defaultTimezone);
    }

    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        $methods = array();

        // Subscribe deserialization and serialization of 'DateTimeImmutable' for 'json', 'xml' and 'php' formats
        foreach (array('json', 'xml', 'php') as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => 'DateTimeImmutable',
                'format' => $format,
            );

            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'DateTimeImmutable',
                'format' => $format,
                'method' => 'serializeDateTimeImmutable',
            );
        }

        return $methods;
    }

    /**
     * @param VisitorInterface $visitor
     * @param DateTimeImmutable $date
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeDateTimeImmutable(
        VisitorInterface $visitor,
        DateTimeImmutable $date,
        array $type,
        Context $context
    ) {
        return $this->serializeDateTime(
            $visitor,
            $this->createMutableDateTimeFromImmutable($date),
            $type,
            $context
        );
    }

    /**
     * @param PhpDeserializationVisitor $visitor
     * @param string $data
     * @param array $type
     * @return DateTimeImmutable|null
     */
    public function deserializeDateTimeImmutableFromPhp(PhpDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->createImmutableDateTimeFromMutable(
            $this->deserializeDateTimeFromPhp($visitor, $data, $type)
        );
    }

    /**
     * @param XmlDeserializationVisitor $visitor
     * @param $data
     * @param array $type
     * @return DateTimeImmutable|null
     */
    public function deserializeDateTimeImmutableFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->createImmutableDateTimeFromMutable(
            $this->deserializeDateTimeFromXml($visitor, $data, $type)
        );
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param $data
     * @param array $type
     * @return DateTimeImmutable|null
     */
    public function deserializeDateTimeImmutableFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->createImmutableDateTimeFromMutable(
            $this->deserializeDateTimeFromJson($visitor, $data, $type)
        );
    }

    /**
     * @param DateTime|null $date
     * @return DateTimeImmutable
     */
    private function createImmutableDateTimeFromMutable(DateTime $date = null)
    {
        if ($date === null) {
            return null;
        }

        if (method_exists(DateTimeImmutable::CLASS, 'createFromMutable')) {
            return DateTimeImmutable::createFromMutable($date);
        }

        // Fallback for PHP < 5.6
        return new DateTimeImmutable($date->format('Y-m-d H:i:s'), $date->getTimezone());
    }

    /**
     * @param DateTimeImmutable|null $date
     * @return DateTime
     */
    private function createMutableDateTimeFromImmutable(DateTimeImmutable $date = null)
    {
        if ($date === null) {
            return null;
        }

        return new DateTime($date->format('Y-m-d H:i:s'), $date->getTimezone());
    }
}
