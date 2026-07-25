<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;

/**
 * Records expenses and reads them back.
 *
 * The service holds the application rules. It is the only place that checks
 * expense values, because entity constructors may only assign properties.
 * Controllers must not call the repository or the entity manager directly.
 * Deptrac enforces this.
 */
final readonly class ExpenseService
{
    public function __construct(private ExpenseRepository $expenseRepository) {}

    /**
     * Records one expense.
     *
     * @throws \InvalidArgumentException If the description is empty or the amount is not positive
     */
    public function record(string $description, int $amountInCents, \DateTimeImmutable $spentOn): Expense
    {
        if ('' === $description || $amountInCents < 1) {
            throw new \InvalidArgumentException('An expense needs a description and a positive amount.');
        }

        $expense = new Expense($description, $amountInCents, $spentOn);
        $this->expenseRepository->add($expense);

        return $expense;
    }

    /**
     * Returns every expense, most recent day first.
     *
     * @return list<Expense>
     */
    public function listNewestFirst(): array
    {
        return $this->expenseRepository->findAllNewestFirst();
    }
}
