<?php

namespace Detail\Normalization\Normalizer;

interface NormalizerInterface
{
    /**
     * @param array $data
     * @param string $class
     * @param array|string $groups
     * @param string|integer $version
     * @return object
     */
    public function denormalize(array $data, $class, $groups = null, $version = null);

    /**
     * @param object $object
     * @param array|string $groups
     * @param string|integer $version
     * @return array
     */
    public function normalize($object, $groups = null, $version = null);
}
