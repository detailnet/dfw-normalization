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
        $serializationContext = new DeserializationContext();

        $this->applyExclusionStrategies($serializationContext, $groups, $version);

        return $this->jmsSerializer->deserialize($data, $class, 'php');
    }

    /**
     * @param mixed $object
     * @param array|string|null $groups
     * @param string|integer|null $version
     * @return array
     */
    public function normalize($object, $groups = null, $version = null)
    {
        return $this->serialize($object, 'php', $groups, $version);
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
        $serializationContext = new SerializationContext();

        $this->applyExclusionStrategies($serializationContext, $groups, $version);

        return $this->jmsSerializer->serialize($object, $format, $serializationContext);
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
