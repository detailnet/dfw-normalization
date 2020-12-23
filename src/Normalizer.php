<?php

declare(strict_types=1);

namespace Detail\Normalization;

interface Normalizer
{
    /**
     * @return array|object
     */
    public function denormalize(array $data, string $class, ?array $groups = null, ?string $version = null);

    /**
     * @param array|object $data
     */
    public function normalize($data, ?array $groups = null, ?string $version = null): array;
}
