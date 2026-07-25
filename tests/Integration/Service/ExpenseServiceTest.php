<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\Service\ExpenseService;
use App\Tests\DatabaseSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
#[CoversClass(ExpenseService::class)]
final class ExpenseServiceTest extends KernelTestCase
{
    use DatabaseSchema;

    public function testItRecordsAnExpense(): void
    {
        $service = $this->service();

        $service->record('Coffee beans', 1299, new \DateTimeImmutable('2026-07-20'));

        $expenses = $service->listNewestFirst();
        self::assertCount(1, $expenses);
        self::assertSame('Coffee beans', $expenses[0]->getDescription());
    }

    public function testItReturnsAnEmptyListWhenNothingIsRecorded(): void
    {
        self::assertSame([], $this->service()->listNewestFirst());
    }

    public function testItAcceptsTheSmallestPositiveAmount(): void
    {
        $service = $this->service();

        $expense = $service->record('Chewing gum', 1, new \DateTimeImmutable('2026-07-20'));

        self::assertSame(1, $expense->getAmountInCents());
    }

    public function testItRejectsAnEmptyDescription(): void
    {
        $service = $this->service();

        $this->expectException(\InvalidArgumentException::class);

        $service->record('', 1299, new \DateTimeImmutable('2026-07-20'));
    }

    public function testItRejectsAnAmountOfZero(): void
    {
        $service = $this->service();

        $this->expectException(\InvalidArgumentException::class);

        $service->record('Coffee beans', 0, new \DateTimeImmutable('2026-07-20'));
    }

    public function testItRejectsANegativeAmount(): void
    {
        $service = $this->service();

        $this->expectException(\InvalidArgumentException::class);

        $service->record('Coffee beans', -1, new \DateTimeImmutable('2026-07-20'));
    }

    public function testItStoresNothingWhenTheAmountIsNotPositive(): void
    {
        $service = $this->service();

        try {
            $service->record('Coffee beans', 0, new \DateTimeImmutable('2026-07-20'));
        } catch (\InvalidArgumentException) {
            self::assertSame([], $service->listNewestFirst());
        }
    }

    private function service(): ExpenseService
    {
        self::bootKernel();
        $this->createDatabaseSchema();
        $service = self::getContainer()->get(ExpenseService::class);
        self::assertInstanceOf(ExpenseService::class, $service);

        return $service;
    }
}
