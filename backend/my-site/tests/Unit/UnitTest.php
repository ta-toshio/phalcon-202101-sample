<?php

declare(strict_types=1);

namespace Tests\Unit;

class UnitTest extends AbstractUnitTest
{
    public function testTestCase(): void
    {
        $this->assertEquals(
            "roman",
            "roman",
            "This will pass"
        );
    }
}
