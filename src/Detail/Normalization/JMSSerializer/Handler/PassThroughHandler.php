<?php

namespace Detail\Normalization\JMSSerializer\Handler;

use Serializable;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;

class PassThroughHandler implements
    SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('php', 'json', 'xml', 'yml');

        foreach ($formats as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => 'passthrough',
                'format' => $format,
                'method' => 'deserializeValue',
            );

            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'passthrough',
                'format' => $format,
                'method' => 'serializeValue',
            );
        }

        return $methods;
    }

    public function deserializeValue(VisitorInterface $visitor, $value, array $type, Context $context)
    {
        return $value;
    }

    /**
     * @param VisitorInterface $visitor
     * @param object $value
     * @param array $type
     * @param Context $context
     * @return mixed
     */
    public function serializeValue(VisitorInterface $visitor, $value, array $type, Context $context)
    {
        // We don't know how to serialize, so we're handling the most common types
        if ($value instanceof Serializable) {
            $value = $value->serialize();
        } elseif (is_callable(array($value, 'toString'))) {
            $value = call_user_func(array($value, 'toString'));
        } elseif (is_callable(array($value, '__toString'))) {
            $value = call_user_func(array($value, '__toString'));
        } else {
            $value = '';
        }

        return $visitor->visitString($value, $type, $context);
    }
}
