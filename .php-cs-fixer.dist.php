<?php

$finder = PhpCsFixer\Finder::create()->in(['src', 'tests', 'examples']);

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'full_opening_tag' => true,
    ])
    ->setFinder($finder);