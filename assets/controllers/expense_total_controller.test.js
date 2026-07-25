import { Application } from "@hotwired/stimulus";
import { afterEach, beforeEach, describe, expect, it } from "vitest";
import ExpenseTotalController from "./expense_total_controller.js";

describe("expense_total_controller", () => {
  let application;

  beforeEach(async () => {
    document.body.innerHTML = `
      <div data-controller="expense-total">
        <ul>
          <li data-expense-total-target="amount" data-amount-in-cents="100"></li>
          <li data-expense-total-target="amount" data-amount-in-cents="250"></li>
          <li data-expense-total-target="amount" data-amount-in-cents="5"></li>
        </ul>
        <output data-expense-total-target="total">0.00</output>
      </div>
    `;

    application = new Application(document.documentElement);
    application.register("expense-total", ExpenseTotalController);
    await application.start();
  });

  afterEach(() => {
    application.stop();
    document.body.innerHTML = "";
  });

  it("writes the total of the listed amounts on connect", () => {
    expect(document.querySelector("output").textContent).toBe("3.55");
  });
});
