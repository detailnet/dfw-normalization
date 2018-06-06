<?php

namespace Detail\Normalization\Normalizer;

interface NormalizerAwareInterface
{
    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer);
}
