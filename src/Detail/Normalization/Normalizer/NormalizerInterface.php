<?php

namespace Detail\Normalization\Normalizer;

interface NormalizerInterface
{
    /**
     * @param array $data
     * @param string $class
     * @param array|string|null $groups
     * @param string|integer|null $version
     * @return object
     */
    public function denormalize(array $data, $class, $groups = null, $version = null);

    /**
     * @param mixed $object
     * @param array|string|null $groups
     * @param string|integer|null $version
     * @return array
     */
    public function normalize($object, $groups = null, $version = null);
}
