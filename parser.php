#!/usr/bin/env php
<?php

use MarkdownParser\Command\ParserCommand;
use MarkdownParser\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;


require __DIR__ . '/vendor/autoload.php';


$app = new Application(new Kernel(true, true));
$app->add(new ParserCommand());
$app->run();