import { Application } from "@hotwired/stimulus";
import { afterEach, beforeEach, describe, expect, it } from "vitest";
import AlertController from "./alert_controller.js";

describe("alert_controller", () => {
  let application;

  beforeEach(async () => {
    document.body.innerHTML = `
      <div data-controller="alert">
        <button data-action="click->alert#dismiss" aria-label="Close">x</button>
      </div>
    `;

    application = new Application(document.documentElement);
    application.register("alert", AlertController);
    await application.start();
  });

  afterEach(() => {
    application.stop();
    document.body.innerHTML = "";
  });

  it("removes the element on dismiss", () => {
    const element = document.querySelector("[data-controller='alert']");

    document.querySelector("button").click();

    expect(document.contains(element)).toBe(false);
  });
});
