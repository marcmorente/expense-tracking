import { Controller } from "@hotwired/stimulus";
/* stimulusFetch: 'lazy' */

export default class extends Controller {
  dismiss() {
    this.element.remove();
  }
}
