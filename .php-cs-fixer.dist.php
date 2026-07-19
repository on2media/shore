<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/lib')
    ->exclude(['cache'])
    ->append([__DIR__.'/.php-cs-fixer.dist.php']);

$config = new PhpCsFixer\Config();
$config
    ->setUsingCache(false)
    ->setRules([
        '@autoPHPMigration' => true,
        '@autoPHPMigration:risky' => true,
        'array_syntax' => false,
        'declare_strict_types' => false,
        'method_argument_space' => false,
        'trailing_comma_in_multiline' => false,
        'void_return' => false,
    ])
    ->setFinder($finder);

return $config;
