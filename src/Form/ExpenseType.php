<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

/**
 * The form that records one expense.
 *
 * The form has no data class. Expense is immutable, so Symfony cannot write into
 * it. The controller reads the submitted values with SubmittedValues.
 */
final class ExpenseType extends AbstractType
{
    /**
     * Builds the expense form.
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('amountInCents', IntegerType::class, [
                'label' => 'Amount in cents',
                'constraints' => [new Positive()],
            ])
            ->add('spentOn', DateType::class, [
                'label' => 'Day',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'constraints' => [new NotNull()],
            ])
            ->add('record', SubmitType::class, [
                'label' => 'Record expense',
            ])
        ;
    }
}
