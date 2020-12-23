<?php

declare(strict_types=1);

namespace Detail\Normalization\Normalizer;

interface NormalizerAwareInterface
{
    public function setNormalizer(NormalizerInterface $normalizer): void;
}
