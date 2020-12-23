<?php

declare(strict_types=1);

namespace Detail\Normalization\JMSSerializer;

use JMS\Serializer\GenericDeserializationVisitor;

class PhpDeserializationVisitor extends GenericDeserializationVisitor
{
    protected function decode($data)
    {
        return $data;
    }
}
