<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ExpenseType;
use App\Form\SubmittedValues;
use App\Service\ExpenseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;
use Symfony\UX\Turbo\TurboStreamResponse;

/**
 * Shows the expense list and accepts new expenses.
 *
 * The controller builds the form, reads the submitted values and calls the
 * service. It holds no application rule. ExpenseType checks the shape of the
 * input. ExpenseService checks the values again, for every caller.
 */
final class ExpenseController extends AbstractController
{
    public function __construct(
        private readonly ExpenseService $expenseService,
        private readonly SubmittedValues $submittedValues,
    ) {}

    /** Shows every recorded expense with an empty form. */
    #[Route('/expenses', name: 'expense_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->renderList($this->createForm(ExpenseType::class), Response::HTTP_OK);
    }

    /** Records one expense and returns to the list. */
    #[Route('/expenses', name: 'expense_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(ExpenseType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->renderList($form, Response::HTTP_BAD_REQUEST);
        }

        $this->expenseService->record(
            $this->submittedValues->text($form, 'description'),
            $this->submittedValues->number($form, 'amountInCents'),
            $this->submittedValues->day($form, 'spentOn'),
        );

        $this->addFlash('success', 'Expense recorded.');

        if (str_contains((string) $request->headers->get('Accept', ''), TurboBundle::STREAM_MEDIA_TYPE)) {
            return new TurboStreamResponse(
                $this->renderView(
                    'expense/create.stream.html.twig',
                    [
                        'expenses' => $this->expenseService->listNewestFirst(),
                        'form' => $this->createForm(ExpenseType::class),
                    ],
                ),
            );
        }

        return $this->redirectToRoute('expense_index');
    }

    /** Renders the list and the form with the given status code. */
    private function renderList(FormInterface $form, int $statusCode): Response
    {
        return $this->render(
            'expense/index.html.twig',
            [
                'expenses' => $this->expenseService->listNewestFirst(),
                'form' => $form,
            ],
            new Response('', $statusCode),
        );
    }
}
