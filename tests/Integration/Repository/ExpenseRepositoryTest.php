<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use App\Tests\DatabaseSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
#[CoversClass(ExpenseRepository::class)]
final class ExpenseRepositoryTest extends KernelTestCase
{
    use DatabaseSchema;

    public function testItStoresAnExpenseAndReadsItBack(): void
    {
        self::bootKernel();
        $this->createDatabaseSchema();
        $repository = $this->repository();

        $repository->add(new Expense('Coffee beans', 1299, new \DateTimeImmutable('2026-07-20')));

        $expenses = $repository->findAllNewestFirst();
        self::assertCount(1, $expenses);
        self::assertSame('Coffee beans', $expenses[0]->getDescription());
        self::assertSame(1299, $expenses[0]->getAmountInCents());
        self::assertSame('2026-07-20', $expenses[0]->getSpentOn()->format('Y-m-d'));
    }

    public function testItReturnsTheMostRecentDayFirst(): void
    {
        self::bootKernel();
        $this->createDatabaseSchema();
        $repository = $this->repository();

        $repository->add(new Expense('Older', 100, new \DateTimeImmutable('2026-07-01')));
        $repository->add(new Expense('Newer', 200, new \DateTimeImmutable('2026-07-31')));

        $expenses = $repository->findAllNewestFirst();
        self::assertCount(2, $expenses);
        self::assertSame('Newer', $expenses[0]->getDescription());
        self::assertSame('Older', $expenses[1]->getDescription());
    }

    public function testItReturnsAnEmptyListWhenNothingIsStored(): void
    {
        self::bootKernel();
        $this->createDatabaseSchema();

        self::assertSame([], $this->repository()->findAllNewestFirst());
    }

    private function repository(): ExpenseRepository
    {
        $repository = self::getContainer()->get(ExpenseRepository::class);
        self::assertInstanceOf(ExpenseRepository::class, $repository);

        return $repository;
    }
}
