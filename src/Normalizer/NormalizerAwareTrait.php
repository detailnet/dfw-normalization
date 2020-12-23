<?php

declare(strict_types=1);

namespace Detail\Normalization\Normalizer;

trait NormalizerAwareTrait
{
    protected NormalizerInterface $normalizer;

    public function getNormalizer(): NormalizerInterface
    {
        return $this->normalizer;
    }

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }
}
