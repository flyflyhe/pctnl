<?php

require "vendor/autoload.php";


$manager = new \x\Manager();

$manager->setTask(new \x\EchoTask(), new \x\EchoTask());


$manager->run();