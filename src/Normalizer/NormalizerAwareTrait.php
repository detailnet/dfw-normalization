<?php

namespace Detail\Normalization\Normalizer;

trait NormalizerAwareTrait
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @return NormalizerInterface
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }
}
