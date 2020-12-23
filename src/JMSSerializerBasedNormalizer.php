<?php

declare(strict_types=1);

namespace Detail\Normalization;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;

class JMSSerializerBasedNormalizer implements Normalizer
{
    private ArrayTransformerInterface $serializer;

    public function __construct(ArrayTransformerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return array|object
     */
    public function denormalize(array $data, string $class, ?array $groups = null, ?string $version = null)
    {
        $context = new DeserializationContext();

        $this->applyExclusionStrategies($context, $groups, $version);

        return $this->serializer->fromArray($data, $class, $context);
    }

    /**
     * @param array|object $data
     */
    public function normalize($data, ?array $groups = null, ?string $version = null): array
    {
        $context = new SerializationContext();

        $this->applyExclusionStrategies($context, $groups, $version);

        return $this->serializer->toArray($data, $context);
    }

    protected function applyExclusionStrategies(Context $context, ?array $groups = null, ?string $version = null): void
    {
        $context->enableMaxDepthChecks();

        if ($groups !== null) {
            $context->setGroups($groups);
        }

        if ($version === null) {
            return;
        }

        $context->setVersion($version);
    }
}
