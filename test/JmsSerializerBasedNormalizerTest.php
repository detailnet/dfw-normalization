<?php

declare(strict_types=1);

namespace DetailTest\Normalization;

use Detail\Normalization\JMSSerializerBasedNormalizer;
use Detail\Normalization\Normalizer;
use Generator;
use JMS\Serializer\ArrayTransformerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class JmsSerializerBasedNormalizerTest extends TestCase
{
    use ProphecyTrait;

    public function provideDenormalizer(): Generator
    {
        $data = ['id' => 'foo'];
        $result = (object) $data;

        $serializer = $this->prophesize(ArrayTransformerInterface::class);
        $serializer->fromArray($data, 'Foo', Argument::any())
            ->willReturn($result);

        yield 'It denormalizes object' => [
            new JMSSerializerBasedNormalizer($serializer->reveal()),
            $data,
            $result,
        ];

        // TODO: Test groups, version, and max-depth.
    }

    /**
     * @param array|object $expectedResult
     *
     * @dataProvider provideDenormalizer
     */
    public function testItDenormalizes(Normalizer $normalizer, array $data, $expectedResult): void
    {
        $this->assertEquals($expectedResult, $normalizer->denormalize($data, 'Foo'));
    }

    // TODO: Test normalization
}
