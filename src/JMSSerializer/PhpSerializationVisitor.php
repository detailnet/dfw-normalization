<?php

namespace Detail\Normalization\JMSSerializer;

use JMS\Serializer\GenericSerializationVisitor;

class PhpSerializationVisitor extends GenericSerializationVisitor
{
    public function getResult()
    {
        return $this->getRoot();
    }
}
