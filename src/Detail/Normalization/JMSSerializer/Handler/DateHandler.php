<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use DateTime;
use DateTimeZone;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler as BaseDateHandler;

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
        $formats = array('php');
        $types = array('DateTime', 'DateInterval');

        foreach ($formats as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => 'DateTime',
                'format' => $format,
            );

            foreach ($types as $type) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => 'serialize' . $type,
                );
            }
        }

        return $methods;
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

        if (strpos($format, '!') !== 0) {
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
