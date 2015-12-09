<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use DateTime;
use DateTimeZone;

use Detail\Normalization\Exception;
use Detail\Normalization\JMSSerializer\PhpDeserializationVisitor;

trait DatePhpDeserializationTrait
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
        $timezone = isset($type['params'][1]) ? new DateTimeZone($type['params'][1]) : $this->timezone;
        $format = isset($type['params'][0]) ? $type['params'][0] : $this->format;

        $datetime = DateTime::createFromFormat($format, (string) $data, $timezone);

        if ($datetime === false) {
            throw new Exception\RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }

        return $datetime;
    }
}
