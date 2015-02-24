<?php

namespace Detail\Normalization\Normalizer;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;

class JMSSerializerBasedNormalizer implements
    NormalizerInterface
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
     * @param array|string $groups
     * @param string|integer $version
     * @return object
     */
    public function denormalize(array $data, $class, $groups = null, $version = null)
    {
        $serializationContext = new DeserializationContext();

        $this->applyExclusionStrategies($serializationContext);

        return $this->jmsSerializer->deserialize($data, $class, 'php');
    }

    /**
     * @param object $object
     * @param array|string $groups
     * @param string|integer $version
     * @return array
     */
    public function normalize($object, $groups = null, $version = null)
    {
        $serializationContext = new SerializationContext();

        $this->applyExclusionStrategies($serializationContext);

        return $this->jmsSerializer->serialize($object, 'php', $serializationContext);
    }

    /**
     * @param Context $context
     * @param array|string $groups
     * @param string|integer $version
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
