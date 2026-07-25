import { Controller } from "@hotwired/stimulus";
import { formatCents, sumAmountsInCents } from "../expense/amount.js";

/*
 * Shows the total of the expenses that the page lists.
 *
 * The template puts this controller on the element that wraps the list and the
 * total. Each list item carries its amount in a data-amount-in-cents attribute.
 */
export default class extends Controller {
  static targets = ["amount", "total"];

  connect() {
    this.showTotal();
  }

  showTotal() {
    const amounts = this.amountTargets.map(
      (element) => element.dataset.amountInCents
    );

    this.totalTarget.textContent = formatCents(sumAmountsInCents(amounts));
  }
}
