<?php
declare(strict_types=1);

use Twig\Loader\FilesystemLoader;
use function DI\autowire;

return [
    FilesystemLoader::class => autowire()
        ->constructorParameter('paths', 'templates'),

    \Twig\Environment::class => autowire()
        ->constructorParameter('loader', \DI\get(FilesystemLoader::class)),
];