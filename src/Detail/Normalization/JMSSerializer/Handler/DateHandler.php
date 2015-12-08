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

class DateHandler extends BaseDateHandler
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var DateTimeZone
     */
    protected $timezone;

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

        // Subscribe deserialization of 'DateTime' for 'php' format
        $methods[] = array(
            'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
            'type' => 'DateTime',
            'format' => 'php',
        );

        // Subscribe serialization of 'DateTime' and 'DateInterval' for 'php' format
        foreach (array('DateTime', 'DateInterval') as $type) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => $type,
                'format' => 'php',
                'method' => 'serialize' . $type,
            );
        }

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
            new DateTime($date->format('Y-m-d H:i:s')),
            $type,
            $context
        );
    }

    /**
     * @param PhpDeserializationVisitor $visitor
     * @param string $data
     * @param array $type
     * @return DateTime|null
     */
    public function deserializeDateTimeFromPhp(PhpDeserializationVisitor $visitor, $data, array $type)
    {
        if ($data === null) {
            return null;
        }

        $format = isset($type['params'][0]) ? $type['params'][0] : $this->format;

        if ($format[0] !== '!') {
            $type['params'][0] = '!' . $format;
        }

        return $this->createDateTime($data, $type);
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

    public function deserializeDateTimeImmutableFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->createImmutableDateTimeFromMutable(
            $this->deserializeDateTimeFromXml($visitor, $data, $type)
        );
    }

    public function deserializeDateTimeImmutableFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->createImmutableDateTimeFromMutable(
            $this->deserializeDateTimeFromJson($visitor, $data, $type)
        );
    }

    /**
     * @param string $data
     * @param array $type
     * @return DateTime
     */
    protected function createDateTime($data, array $type)
    {
        $timezone = isset($type['params'][1]) ? new \DateTimeZone($type['params'][1]) : $this->timezone;
        $format = isset($type['params'][0]) ? $type['params'][0] : $this->format;

        $datetime = DateTime::createFromFormat($format, (string) $data, $timezone);

        if ($datetime === false) {
            throw new Exception\RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }

        return $datetime;
    }

    /**
     * @param DateTime $date
     * @return DateTimeImmutable
     */
    private function createImmutableDateTimeFromMutable(DateTime $date)
    {
        if (method_exists(DateTimeImmutable::CLASS, 'createFromMutable')) {
            return DateTimeImmutable::createFromMutable($date);
        }

        // Fallback for PHP < 5.6
        return DateTimeImmutable::createFromFormat('U', $date->format('U'));
    }
}
