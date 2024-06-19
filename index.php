<?php

require 'utils/RYMGenerator.php';

$rymGen = new RYMGenerator();

echo '<a href="./'.str_replace(__DIR__,'',$rymGen->config->getMVCPath()).'public/" target="_blank"> View MVC Site </a>';

echo $rymGen->createMVCBase();
echo $rymGen->generateModelAll();
echo $rymGen->generateControllerAll();
echo $rymGen->generateViewsAll();
