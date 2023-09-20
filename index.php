<?php

require 'utils/RYMGenerator.php';

$rymGen = new RYMGenerator();

#echo '<br>Final: ' . $rymGen->getModelNameClass('applicantsEconomicLevel');
echo '<a href="./'.str_replace(__DIR__,'',$rymGen->mvcPath).'public/?method=RYMAPPAdministratorsStatusCatalog&action=index&actionType=viewForm" target="_blank"> View </a>';
echo '<a href="./'.str_replace(__DIR__,'',$rymGen->mvcPath).'public/api.php?method=RYMAPPAdministratorsStatusCatalog&action=index&actionType=API1.1" target="_blank"> View API </a>';

echo $rymGen->createMVCBase();
echo $rymGen->generateModelAll();
echo $rymGen->generateControllerAll();
echo $rymGen->generateViewsAll();
