<?php

require_once __DIR__ . '/../vendor/autoload.php';

putenv('FORSETI_SENTRY_DNS=https://5e8b5929ee114897bc5fed17d54fbb34:2cda421e43384bed85c1db7b7aca17b7@sentry.io/175458');
putenv('FORSETI_SENTRY_LOGGER_LEVEL=' . Monolog\Logger::ERROR);

$logger = new \Forseti\Logger\Logger('teste');
$logger->info('mostrar na tela');

throw new Exception('sentry error');