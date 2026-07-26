import { Application } from "@hotwired/stimulus";
import { afterEach, beforeEach, describe, expect, it } from "vitest";
import FlashController from "./flash_controller.js";

describe("flash_controller", () => {
  let application;

  beforeEach(async () => {
    document.body.innerHTML = `
      <div data-controller="flash">
        <button data-action="click->flash#dismiss" aria-label="Close">x</button>
      </div>
    `;

    application = new Application(document.documentElement);
    application.register("flash", FlashController);
    await application.start();
  });

  afterEach(() => {
    application.stop();
    document.body.innerHTML = "";
  });

  it("removes the element on dismiss", () => {
    const element = document.querySelector("[data-controller='flash']");

    document.querySelector("button").click();

    expect(document.contains(element)).toBe(false);
  });
});
