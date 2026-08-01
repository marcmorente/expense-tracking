<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Controller\ExpenseController;
use App\Tests\DatabaseSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\TwigComponent\Test\InteractsWithTwigComponents;

/**
 * @internal
 */
#[CoversClass(ExpenseController::class)]
final class ExpenseControllerTest extends WebTestCase
{
    use DatabaseSchema;
    use InteractsWithTwigComponents;

    public function testItShowsAnEmptyListAndAForm(): void
    {
        $client = $this->clientWithSchema();

        $client->request('GET', '/expenses');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.expense-empty', 'No expenses yet.');
        self::assertSelectorExists('turbo-frame#expenses');
        self::assertSelectorExists('form input[name="expense[description]"]');
        self::assertSelectorExists('form[data-turbo-frame="expenses"]');
    }

    public function testItRendersEveryAlertType(): void
    {
        $palettes = [
            'success' => 'bg-green-50',
            'error' => 'bg-red-50',
            'warning' => 'bg-yellow-50',
            'info' => 'bg-blue-50',
            'unknown' => 'bg-gray-50',
        ];

        foreach ($palettes as $type => $background) {
            $rendered = $this->renderTwigComponent(
                'Alert',
                ['type' => $type, 'message' => 'Example alert'],
            );
            $alert = $rendered->crawler()->filter('[role="alert"]');

            self::assertCount(1, $alert);
            self::assertStringContainsString($background, (string) $alert->attr('class'));
            self::assertStringContainsString('Example alert', $alert->text());
        }
    }

    public function testItOnlyRendersAnAlertCloseButtonWhenDismissible(): void
    {
        $dismissible = $this->renderTwigComponent(
            'Alert',
            ['message' => 'Dismissible alert', 'dismissible' => true],
        )->crawler();
        $notDismissible = $this->renderTwigComponent(
            'Alert',
            ['message' => 'Persistent alert'],
        )->crawler();

        self::assertCount(1, $dismissible->filter('button[data-action="click->alert#dismiss"]'));
        self::assertCount(0, $notDismissible->filter('button'));
    }

    public function testItRecordsAnExpenseAndShowsItInTheList(): void
    {
        $client = $this->clientWithSchema();
        $client->request('GET', '/expenses');

        $client->submitForm('Record expense', [
            'expense[description]' => 'Coffee beans',
            'expense[amountInCents]' => '1299',
            'expense[spentOn]' => '2026-07-20',
        ]);

        self::assertResponseRedirects('/expenses');
        $client->followRedirect();
        self::assertSelectorTextContains('.expense-description', 'Coffee beans');
        self::assertSelectorTextContains('.expense-amount', '€12.99');
        self::assertSelectorTextContains('[role="status"]', 'Expense recorded.');
    }

    public function testItUpdatesTheListWithATurboStream(): void
    {
        $client = $this->clientWithSchema();
        $client->request('GET', '/expenses');

        $client->submitForm(
            'Record expense',
            [
                'expense[description]' => 'Coffee beans',
                'expense[amountInCents]' => '1299',
                'expense[spentOn]' => '2026-07-20',
            ],
            'POST',
            ['HTTP_ACCEPT' => 'text/vnd.turbo-stream.html'],
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'text/vnd.turbo-stream.html; charset=UTF-8');
        $responseContent = $client->getResponse()->getContent();
        self::assertIsString($responseContent);
        self::assertStringContainsString('targets="#expense-list"', $responseContent);
        self::assertStringContainsString('Coffee beans', $responseContent);
        self::assertStringContainsString('action="append"', $responseContent);
        self::assertStringContainsString('targets="#flash-messages"', $responseContent);
        self::assertStringContainsString('Expense recorded.', $responseContent);
    }

    public function testItRendersToastRolesForNormalAndErrorMessages(): void
    {
        $success = $this->renderTwigComponent(
            'Toast',
            ['type' => 'success', 'message' => 'Saved'],
        )->crawler();
        $error = $this->renderTwigComponent(
            'Toast',
            ['type' => 'error', 'message' => 'Failed'],
        )->crawler();

        self::assertSame('status', $success->filter('[role="status"]')->attr('role'));
        self::assertSame('alert', $error->filter('[role="alert"]')->attr('role'));
        self::assertSame('5000', $success->filter('[data-controller="toast"]')->attr('data-toast-duration-value'));
    }

    public function testItRejectsABlankDescription(): void
    {
        $client = $this->clientWithSchema();
        $client->request('GET', '/expenses');

        $client->submitForm('Record expense', [
            'expense[description]' => '',
            'expense[amountInCents]' => '1299',
            'expense[spentOn]' => '2026-07-20',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertSelectorTextContains('.expense-empty', 'No expenses yet.');
    }

    public function testItRejectsAnAmountOfZero(): void
    {
        $client = $this->clientWithSchema();
        $client->request('GET', '/expenses');

        $client->submitForm('Record expense', [
            'expense[description]' => 'Coffee beans',
            'expense[amountInCents]' => '0',
            'expense[spentOn]' => '2026-07-20',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertSelectorTextContains('.expense-empty', 'No expenses yet.');
    }

    public function testItRejectsAMalformedDay(): void
    {
        $client = $this->clientWithSchema();
        $client->request('GET', '/expenses');

        $client->submitForm('Record expense', [
            'expense[description]' => 'Coffee beans',
            'expense[amountInCents]' => '1299',
            'expense[spentOn]' => '20 July 2026',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertSelectorTextContains('.expense-empty', 'No expenses yet.');
    }

    public function testItRejectsARequestWithoutASubmittedForm(): void
    {
        $client = $this->clientWithSchema();

        $client->request('POST', '/expenses');

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Returns a client that keeps one in-memory database for every request.
     */
    private function clientWithSchema(): KernelBrowser
    {
        $client = self::createClient();
        $client->disableReboot();
        $this->createDatabaseSchema();

        return $client;
    }
}
