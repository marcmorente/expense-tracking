<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Stores and reads expenses.
 *
 * The class does not extend ServiceEntityRepository. It receives the entity
 * manager through the constructor, so it stays final and immutable.
 */
final readonly class ExpenseRepository
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function add(Expense $expense): void
    {
        $this->entityManager->persist($expense);
        $this->entityManager->flush();
    }

    /**
     * Returns every expense, most recent day first.
     *
     * @return list<Expense>
     */
    public function findAllNewestFirst(): array
    {
        return $this->entityManager
            ->getRepository(Expense::class)
            ->findBy([], ['spentOn' => 'DESC'])
        ;
    }
}
