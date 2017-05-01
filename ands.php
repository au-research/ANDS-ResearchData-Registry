#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

restore_error_handler();
date_default_timezone_set('UTC');

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new \ANDS\Commands\ConceptsCommand());
$application->add(new \ANDS\Commands\RegistryObject\RegistryObjectGetCommand());
$application->add(new \ANDS\Commands\Scholix\ScholixProcessCommand());
$application->add(new \ANDS\Commands\DOISyncCommand());
$application->add(new \ANDS\Commands\RegistryObject\RegistryObjectSyncCommand());
$application->add(new \ANDS\Commands\RegistryObject\RegistryObjectProcessCommand());
$application->add(new \ANDS\Commands\SyncRecordWorkerRedisCommand());
$application->add(new \ANDS\Commands\RunScriptCommand());

$application->run();