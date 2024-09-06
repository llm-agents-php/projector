<?php

declare(strict_types=1);

use LLM\Assistant\Bootstrap;
use PHPUnit\Architecture\Elements\ObjectDescription;
use PHPUnit\Framework\TestCase;

final class ArchTest extends TestCase
{
    use PHPUnit\Architecture\ArchitectureAsserts;

    public function testInternals(): void
    {
        $internals = $this->layer()->leave(
            static fn(ObjectDescription $a) => \str_contains($a->name, '\\Internal\\'),
        );
        $externals = $this->layer()->leave(
            static fn(ObjectDescription $a) => !\str_contains(
                    $a->name,
                    '\\Internal\\',
                ) && $a->name !== Bootstrap::class,
        );

        $this->assertDoesNotDependOn($externals, $internals);
    }
}
