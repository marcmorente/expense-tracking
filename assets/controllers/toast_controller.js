import { Controller } from "@hotwired/stimulus";
/* stimulusFetch: 'lazy' */

export default class extends Controller {
  static values = {
    duration: {
      type: Number,
      default: 5000,
    },
  };

  connect() {
    this.pointerInside = false;
    this.focusInside = false;
    this.remainingDuration = this.durationValue;
    this.timerId = null;
    this.startedAt = null;

    this.trimToasts();
    this.startTimer();
  }

  disconnect() {
    this.clearTimer();
  }

  dismiss() {
    this.element.remove();
  }

  pointerEnter() {
    this.pointerInside = true;
    this.pauseTimer();
  }

  pointerLeave() {
    this.pointerInside = false;
    this.resumeTimer();
  }

  focusEnter() {
    this.focusInside = true;
    this.pauseTimer();
  }

  focusLeave(event) {
    if (
      event.relatedTarget instanceof Node &&
      this.element.contains(event.relatedTarget)
    ) {
      return;
    }

    this.focusInside = false;
    this.resumeTimer();
  }

  startTimer() {
    if (this.durationValue === 0 || this.timerId !== null || this.isPaused()) {
      return;
    }

    this.startedAt = Date.now();
    this.timerId = window.setTimeout(
      () => this.dismiss(),
      this.remainingDuration
    );
  }

  pauseTimer() {
    if (this.timerId === null) {
      return;
    }

    this.clearTimer();
    this.remainingDuration -= Date.now() - this.startedAt;
  }

  resumeTimer() {
    if (this.isPaused() || this.remainingDuration <= 0) {
      return;
    }

    this.startTimer();
  }

  clearTimer() {
    if (this.timerId === null) {
      return;
    }

    window.clearTimeout(this.timerId);
    this.timerId = null;
  }

  isPaused() {
    return this.pointerInside || this.focusInside;
  }

  trimToasts() {
    const container = this.element.parentElement;

    if (container === null) {
      return;
    }

    const toasts = [
      ...container.querySelectorAll('[data-controller~="toast"]'),
    ];

    while (toasts.length > 5) {
      toasts.shift().remove();
    }
  }
}
