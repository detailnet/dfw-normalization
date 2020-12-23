<?php

declare(strict_types=1);

namespace Detail\Normalization\Normalizer;

trait NormalizerAwareTrait
{
    protected Normalizer $normalizer;

    public function getNormalizer(): Normalizer
    {
        return $this->normalizer;
    }

    public function setNormalizer(Normalizer $normalizer): void
    {
        $this->normalizer = $normalizer;
    }
}
