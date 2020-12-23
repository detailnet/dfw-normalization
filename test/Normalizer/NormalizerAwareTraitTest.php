<?php

declare(strict_types=1);

namespace DetailTest\Normalization\Normalizer;

use Detail\Normalization\Normalizer\Normalizer;
use Detail\Normalization\Normalizer\NormalizerAwareTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function assert;

class NormalizerAwareTraitTest extends TestCase
{
    private MockObject $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = $this->getMockBuilder(Normalizer::CLASS)->getMock();
    }

    public function testSetsNormalizer(): void
    {
        $normalizer = $this->getNormalizer();
        assert($normalizer instanceof Normalizer);

        $object = $this->getMockBuilder(NormalizerAwareTrait::CLASS)->getMockForTrait();
        assert($object instanceof NormalizerAwareTrait);

        $this->assertNull($object->getNormalizer());

        $object->setNormalizer($normalizer);

        $this->assertEquals($normalizer, $object->getNormalizer());
    }

    private function getNormalizer(): MockObject
    {
        return $this->normalizer;
    }
}
