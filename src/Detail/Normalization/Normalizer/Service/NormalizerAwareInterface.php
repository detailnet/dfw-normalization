<?php

namespace Detail\Normalization\Normalizer\Service;

use Detail\Normalization\Normalizer\NormalizerInterface;

interface NormalizerAwareInterface
{
    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer);
}
