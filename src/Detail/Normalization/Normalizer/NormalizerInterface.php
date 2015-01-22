<?php

namespace Detail\Normalization\Normalizer;

interface NormalizerInterface
{
    public function denormalize(array $data, $class);

    public function normalize($object);
}
