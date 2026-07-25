<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

/**
 * One amount of money that the user spent on one day.
 *
 * The entity is pure data. It holds no rule, because a constructor may only
 * assign properties (haspadar.constructorInit). ExpenseService checks the
 * values before it creates an expense. The entity creates its own identifier,
 * so the constructor stays inside the three-parameter limit.
 */
#[ORM\Entity]
#[ORM\Table(name: 'expense')]
final class Expense
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private readonly Uuid $id;

    #[ORM\Column(type: Types::STRING)]
    private readonly string $description;

    #[ORM\Column(type: Types::INTEGER)]
    private readonly int $amountInCents;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private readonly \DateTimeImmutable $spentOn;

    public function __construct(string $description, int $amountInCents, \DateTimeImmutable $spentOn)
    {
        $this->id = new UuidV7();
        $this->description = $description;
        $this->amountInCents = $amountInCents;
        $this->spentOn = $spentOn;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    public function getSpentOn(): \DateTimeImmutable
    {
        return $this->spentOn;
    }
}
