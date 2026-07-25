<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->exclude('assets/vendor')
    ->exclude('public/assets')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return (new Config())
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
