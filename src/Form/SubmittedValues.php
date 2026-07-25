<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\FormInterface;

/**
 * Reads typed values from a submitted form.
 *
 * FormInterface::getData() returns mixed, and PHPStan runs at level 10. This
 * class narrows each value once, in one place, instead of once in every
 * controller. Call it only after the form reports that it is valid.
 */
final readonly class SubmittedValues
{
    /**
     * Returns the string that the given field holds.
     *
     * @throws \InvalidArgumentException If the field does not hold a string
     */
    public function text(FormInterface $form, string $field): string
    {
        $value = $form->get($field)->getData();

        if (!\is_string($value)) {
            throw new \InvalidArgumentException(\sprintf('The field %s must hold a string.', $field));
        }

        return $value;
    }

    /**
     * Returns the integer that the given field holds.
     *
     * @throws \InvalidArgumentException If the field does not hold an integer
     */
    public function number(FormInterface $form, string $field): int
    {
        $value = $form->get($field)->getData();

        if (!\is_int($value)) {
            throw new \InvalidArgumentException(\sprintf('The field %s must hold an integer.', $field));
        }

        return $value;
    }

    /**
     * Returns the date that the given field holds.
     *
     * @throws \InvalidArgumentException If the field does not hold a date
     */
    public function day(FormInterface $form, string $field): \DateTimeImmutable
    {
        $value = $form->get($field)->getData();

        if (!$value instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException(\sprintf('The field %s must hold a date.', $field));
        }

        return $value;
    }
}
