<?php

declare(strict_types=1);

namespace LLM\Assistant\Tests\Unit;

use LLM\Assistant\Info;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

final class InfoTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testVersionDoesntFail(): void
    {
        Info::version();
    }
}
