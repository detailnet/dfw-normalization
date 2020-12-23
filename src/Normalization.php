<?php

declare(strict_types=1);

namespace Detail\Normalization;

interface Normalization
{
    public function setNormalizer(Normalizer $normalizer): void;
}
