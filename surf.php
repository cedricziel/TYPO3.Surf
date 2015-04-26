#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

Dotenv::load(__DIR__);

use TYPO3\Surf\Command\ListCommand;
use TYPO3\Surf\Command\DeployCommand;
use TYPO3\Surf\Command\DescribeCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new ListCommand());
$application->add(new DeployCommand());
$application->add(new DescribeCommand());
$application->run();
