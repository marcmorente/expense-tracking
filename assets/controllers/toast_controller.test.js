import { Application } from "@hotwired/stimulus";
import { afterEach, beforeEach, describe, expect, it, vi } from "vitest";
import ToastController from "./toast_controller.js";

describe("toast_controller", () => {
  let application;

  beforeEach(async () => {
    vi.useFakeTimers();
    application = new Application(document.documentElement);
    application.register("toast", ToastController);
  });

  afterEach(() => {
    application.stop();
    document.body.innerHTML = "";
    vi.useRealTimers();
  });

  it("dismisses after the default duration", async () => {
    document.body.innerHTML = '<div data-controller="toast"></div>';
    await application.start();

    vi.advanceTimersByTime(4999);
    expect(document.querySelector('[data-controller="toast"]')).not.toBeNull();

    vi.advanceTimersByTime(1);
    expect(document.querySelector('[data-controller="toast"]')).toBeNull();
  });

  it("uses a custom duration", async () => {
    document.body.innerHTML =
      '<div data-controller="toast" data-toast-duration-value="3000"></div>';
    await application.start();

    vi.advanceTimersByTime(2999);
    expect(document.querySelector('[data-controller="toast"]')).not.toBeNull();

    vi.advanceTimersByTime(1);
    expect(document.querySelector('[data-controller="toast"]')).toBeNull();
  });

  it("does not dismiss when the duration is zero", async () => {
    document.body.innerHTML =
      '<div data-controller="toast" data-toast-duration-value="0"></div>';
    await application.start();

    vi.advanceTimersByTime(10000);

    expect(document.querySelector('[data-controller="toast"]')).not.toBeNull();
  });

  it("dismisses when the close action runs", async () => {
    document.body.innerHTML = `
      <div data-controller="toast">
        <button data-action="click->toast#dismiss" aria-label="Close">x</button>
      </div>
    `;
    await application.start();

    document.querySelector("button").click();

    expect(document.querySelector('[data-controller="toast"]')).toBeNull();
  });

  it("pauses while the pointer is over the Toast", async () => {
    document.body.innerHTML = '<div data-controller="toast"></div>';
    await application.start();
    const toast = document.querySelector('[data-controller="toast"]');
    const controller = application.getControllerForElementAndIdentifier(
      toast,
      "toast"
    );

    vi.advanceTimersByTime(1000);
    controller.pointerEnter();
    vi.advanceTimersByTime(5000);
    expect(document.contains(toast)).toBe(true);

    controller.pointerLeave();
    vi.advanceTimersByTime(3999);
    expect(document.contains(toast)).toBe(true);

    vi.advanceTimersByTime(1);
    expect(document.contains(toast)).toBe(false);
  });

  it("pauses while the Toast has keyboard focus", async () => {
    document.body.innerHTML = `
      <div data-controller="toast">
        <button>Close</button>
      </div>
    `;
    await application.start();
    const toast = document.querySelector('[data-controller="toast"]');
    const controller = application.getControllerForElementAndIdentifier(
      toast,
      "toast"
    );

    vi.advanceTimersByTime(1000);
    controller.focusEnter();
    vi.advanceTimersByTime(5000);
    expect(document.contains(toast)).toBe(true);

    controller.focusLeave({ relatedTarget: document.body });
    vi.advanceTimersByTime(3999);
    expect(document.contains(toast)).toBe(true);

    vi.advanceTimersByTime(1);
    expect(document.contains(toast)).toBe(false);
  });

  it("keeps only the five newest Toasts", async () => {
    document.body.innerHTML = Array.from(
      { length: 6 },
      (_, index) =>
        `<div data-controller="toast" data-toast-id="${index + 1}"></div>`
    ).join("");
    await application.start();

    const toasts = document.querySelectorAll('[data-controller="toast"]');

    expect(toasts).toHaveLength(5);
    expect(document.querySelector('[data-toast-id="1"]')).toBeNull();
    expect(document.querySelector('[data-toast-id="6"]')).not.toBeNull();
  });
});
