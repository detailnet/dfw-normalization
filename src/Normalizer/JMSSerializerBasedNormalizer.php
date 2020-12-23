<?php

declare(strict_types=1);

namespace Detail\Normalization\Normalizer;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use function assert;
use function is_array;

class JMSSerializerBasedNormalizer implements
    NormalizerInterface,
    SerializerInterface
{
    protected Serializer $jmsSerializer;

    public function __construct(Serializer $jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @param array $data
     * @param array|string|null $groups
     * @param string|int|null $version
     *
     * @return mixed
     */
    public function denormalize(array $data, string $class, $groups = null, $version = null)
    {
        $context = new DeserializationContext();

        $this->applyExclusionStrategies($context, $groups, $version);

        return $this->jmsSerializer->deserialize($data, $class, 'php', $context);
    }

    /**
     * @param mixed $object
     * @param array|string|null $groups
     * @param string|int|null $version
     *
     * @return array
     */
    public function normalize($object, $groups = null, $version = null): array
    {
        $data = $this->serialize($object, 'php', $groups, $version);
        assert(is_array($data));

        return $data;
    }

    /**
     * @param mixed $object
     * @param array|string|null $groups
     * @param string|int|null $version
     */
    public function serialize($object, string $format, $groups = null, $version = null): string
    {
        $context = new SerializationContext();

        $this->applyExclusionStrategies($context, $groups, $version);

        return $this->jmsSerializer->serialize($object, $format, $context);
    }

    /**
     * @param array|string|null $groups
     * @param string|int|null $version
     */
    protected function applyExclusionStrategies(Context $context, $groups = null, $version = null): void
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
