import { describe, expect, it } from "vitest";
import { formatCents, sumAmountsInCents } from "./amount.js";

describe("sumAmountsInCents", () => {
  it("returns zero for an empty list", () => {
    expect(sumAmountsInCents([])).toBe(0);
  });

  it("adds numeric amounts", () => {
    expect(sumAmountsInCents([100, 250, 5])).toBe(355);
  });

  it("adds amounts that come from data attributes as strings", () => {
    expect(sumAmountsInCents(["100", "250"])).toBe(350);
  });
});

describe("formatCents", () => {
  it("keeps two decimal places", () => {
    expect(formatCents(0)).toBe("0.00");
    expect(formatCents(5)).toBe("0.05");
    expect(formatCents(1234)).toBe("12.34");
  });
});
