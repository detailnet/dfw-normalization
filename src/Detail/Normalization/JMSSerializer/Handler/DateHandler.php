<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use DateTime;
use DateTimeZone;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler as BaseDateHandler;

class DateHandler extends BaseDateHandler
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

        return $methods;
    }
}
