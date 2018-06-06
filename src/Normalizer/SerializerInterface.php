<?php

namespace Detail\Normalization\Normalizer;

interface SerializerInterface
{
    /**
     * @param mixed $object
     * @param string $format
     * @param array|string $groups
     * @param string|integer $version
     * @return string
     */
    public function serialize($object, $format, $groups = null, $version = null);
}
