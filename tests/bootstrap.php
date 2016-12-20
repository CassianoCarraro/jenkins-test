<?php

chdir(dirname(__DIR__));

include('tests/_autoload.php');

AutoLoader::registerDirectory(getcwd() . '/src');