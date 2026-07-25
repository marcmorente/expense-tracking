<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Expense;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Expense::class)]
final class ExpenseTest extends TestCase
{
    public function testItKeepsTheValuesThatItReceives(): void
    {
        $spentOn = new \DateTimeImmutable('2026-07-25');

        $expense = new Expense('Coffee beans', 1299, $spentOn);

        self::assertSame('Coffee beans', $expense->getDescription());
        self::assertSame(1299, $expense->getAmountInCents());
        self::assertSame($spentOn, $expense->getSpentOn());
    }

    public function testItCreatesADifferentIdentifierForEachExpense(): void
    {
        $spentOn = new \DateTimeImmutable('2026-07-25');

        $first = new Expense('Coffee beans', 1299, $spentOn);
        $second = new Expense('Coffee beans', 1299, $spentOn);

        self::assertFalse($first->getId()->equals($second->getId()));
    }
}
