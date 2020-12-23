<?php

declare(strict_types=1);

namespace Detail\Normalization\Normalizer;

interface NormalizerInterface
{
    /**
     * @param array $data
     * @param array|string|null $groups
     * @param string|int|null $version
     */
    public function denormalize(array $data, string $class, $groups = null, $version = null): object;

    /**
     * @param mixed $object
     * @param array|string|null $groups
     * @param string|int|null $version
     *
     * @return array
     */
    public function normalize($object, $groups = null, $version = null): array;
}
