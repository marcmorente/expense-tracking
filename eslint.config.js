const js = require("@eslint/js");
const globals = require("globals");

module.exports = [
  {
    ignores: ["node_modules/**", "assets/vendor/**", "var/**"],
  },
  js.configs.recommended,
  {
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
      },
    },
    rules: {
      camelcase: "error",
      curly: "error",
      "dot-notation": "error",
      eqeqeq: "error",
      "no-alert": "warn",
      "no-console": "warn",
      "no-else-return": ["error", { allowElseIf: false }],
      "no-eval": "error",
      "no-nested-ternary": "error",
      "no-param-reassign": "error",
      "no-plusplus": "error",
      "no-shadow": "error",
      "no-unneeded-ternary": ["error", { defaultAssignment: false }],
      "no-useless-concat": "error",
      "no-useless-return": "error",
      "no-var": "error",
      "object-shorthand": "error",
      "one-var": ["error", "never"],
      "prefer-arrow-callback": "error",
      "prefer-const": "error",
      "prefer-destructuring": [
        "error",
        {
          VariableDeclarator: { array: false, object: true },
          AssignmentExpression: { array: true, object: false },
        },
      ],
      "prefer-template": "error",
    },
  },
];
