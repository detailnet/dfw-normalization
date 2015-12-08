<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use DateTime;
use DateTimeZone;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler as BaseDateHandler;
use JMS\Serializer\VisitorInterface;

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

        // Register deserialization of 'DateTime' for 'php' format
        $methods[] = array(
            'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
            'type' => 'DateTime',
            'format' => 'php',
        );

        // Register serialization of 'DateTime' and 'DateInterval' for 'php' format
        foreach (array('DateTime', 'DateInterval') as $type) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => $type,
                'format' => 'php',
                'method' => 'serialize' . $type,
            );
        }

        // Register serialization of 'DateTimeImmutable' for all formats
        foreach (array('json', 'xml', 'yml', 'php') as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'DateTimeImmutable',
                'format' => $format,
                'method' => 'serializeDateTimeImmutable' ,
            );
        }

        return $methods;
    }

    /**
     * @param VisitorInterface $visitor
     * @param \DateTimeImmutable $date
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeDateTimeImmutable(
        VisitorInterface $visitor,
        \DateTimeImmutable $date,
        array $type,
        Context $context
    ) {
        return $this->serializeDateTime(
            $visitor,
            new \DateTime($date->format('Y-m-d H:i:s')),
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
}
