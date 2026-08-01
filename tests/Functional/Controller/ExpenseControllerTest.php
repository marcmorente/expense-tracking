<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Controller\ExpenseController;
use App\Tests\DatabaseSchema;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[CoversClass(ExpenseController::class)]
final class ExpenseControllerTest extends WebTestCase
{
    use DatabaseSchema;

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
        self::assertSelectorTextContains('[role="alert"]', 'Expense recorded.');
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
        self::assertStringContainsString('targets="#flash-messages"', $responseContent);
        self::assertStringContainsString('Expense recorded.', $responseContent);
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
