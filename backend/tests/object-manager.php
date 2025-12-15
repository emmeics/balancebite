<?php

declare(strict_types=1);

/*
 * Questo file è usato da PHPStan per caricare l'EntityManager di Doctrine.
 * Permette a PHPStan di analizzare correttamente le query e i repository.
 */

require __DIR__.'/../vendor/autoload.php';

$kernel = new App\Kernel('dev', true);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
