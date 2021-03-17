<?php

require '../vendor/autoload.php';

$dataClass = new \CBSOpenData\Data();
$residences = $dataClass->getResidences();

foreach ($residences as $residence) {
    echo $residence->get('Key').' '.$residence->get('Title').'<br/>';
}