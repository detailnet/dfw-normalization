<?php

namespace Detail\Normalization\Normalizer;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class JMSSerializerBasedNormalizer implements
    NormalizerInterface,
    SerializerInterface
{
    /**
     * @var Serializer
     */
    protected $jmsSerializer;

    /**
     * @param Serializer $jmsSerializer
     */
    public function __construct(Serializer $jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @param array $data
     * @param string $class
     * @param array|string|null $groups
     * @param string|integer|null $version
     * @return mixed
     */
    public function denormalize(array $data, $class, $groups = null, $version = null)
    {
        $context = new DeserializationContext();

        $this->applyExclusionStrategies($context, $groups, $version);

        return $this->jmsSerializer->deserialize($data, $class, 'php', $context);
    }

    /**
     * @param mixed $object
     * @param array|string|null $groups
     * @param string|integer|null $version
     * @return array
     */
    public function normalize($object, $groups = null, $version = null)
    {
        /** @var array $data */
        $data = $this->serialize($object, 'php', $groups, $version);

        return $data;
    }

    /**
     * @param mixed $object
     * @param string $format
     * @param array|string|null $groups
     * @param string|integer|null $version
     * @return string
     */
    public function serialize($object, $format, $groups = null, $version = null)
    {
        $context = new SerializationContext();

        $this->applyExclusionStrategies($context, $groups, $version);

        return $this->jmsSerializer->serialize($object, $format, $context);
    }

    /**
     * @param Context $context
     * @param array|string|null $groups
     * @param string|integer|null $version
     */
    protected function applyExclusionStrategies(Context $context, $groups = null, $version = null)
    {
        $context->enableMaxDepthChecks();

        if ($groups !== null) {
            $context->setGroups($groups);
        }

        if ($version !== null) {
            $context->setVersion($version);
        }
    }
}
