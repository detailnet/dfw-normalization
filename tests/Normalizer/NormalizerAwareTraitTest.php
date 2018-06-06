<?php

namespace DetailTest\Normalization\Normalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Detail\Normalization\Normalizer\NormalizerAwareTrait;
use Detail\Normalization\Normalizer\NormalizerInterface;

class NormalizerAwareTraitTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = $this->getMockBuilder(NormalizerInterface::CLASS)->getMock();
    }

    public function testSetsNormalizer(): void
    {
        /** @var NormalizerInterface $normalizer */
        $normalizer = $this->getNormalizer();

        /** @var NormalizerAwareTrait $object */
        $object = $this->getMockBuilder(NormalizerAwareTrait::CLASS)->getMockForTrait();

        $this->assertNull($object->getNormalizer());

        $object->setNormalizer($normalizer);

        $this->assertEquals($normalizer, $object->getNormalizer());
    }

    private function getNormalizer(): MockObject
    {
        return $this->normalizer;
    }
}
