<?php

declare(strict_types=1);

namespace Detail\Normalization\Normalizer;

interface SerializerInterface
{
    /**
     * @param mixed $object
     * @param array|string $groups
     * @param string|int $version
     */
    public function serialize($object, string $format, $groups = null, $version = null): string;
}
