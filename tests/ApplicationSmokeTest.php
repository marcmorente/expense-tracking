<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ApplicationSmokeTest extends KernelTestCase
{
    public function testKernelBootsIntoTestEnvironment(): void
    {
        $kernel = self::bootKernel();
        $environment = $kernel->getEnvironment();
        self::assertSame('test', $environment);
    }
}
