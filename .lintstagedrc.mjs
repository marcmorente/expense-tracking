export default {
  "*.php": (filenames) => {
    const filtered = filenames.filter((f) => f !== "config/reference.php");

    if (filtered.length === 0) {
      return [];
    }

    return `vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php ${filtered.join(" ")}`;
  },
  "*.js": "eslint --fix",
  "*.css": "prettier --write",
  "*.{json,yaml,yml,js,ts,jsx,tsx,scss}": "prettier --write",
};
