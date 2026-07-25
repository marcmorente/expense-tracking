const CENTS_PER_UNIT = 100;

/**
 * Adds amounts that are given in cents.
 *
 * @param {Array<number|string>} values Amounts in cents.
 * @returns {number} The sum in cents.
 */
export function sumAmountsInCents(values) {
  return values.reduce((total, value) => total + Number(value), 0);
}

/**
 * Formats an amount in cents as a decimal string with two decimal places.
 *
 * @param {number} cents Amount in cents.
 * @returns {string} The formatted amount.
 */
export function formatCents(cents) {
  return (cents / CENTS_PER_UNIT).toFixed(2);
}
