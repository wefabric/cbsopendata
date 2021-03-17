<?php

require '../vendor/autoload.php';

set_time_limit(0); // safe_mode is off
ini_set('max_execution_time', 0); //500 seconds
$combined = new \CBSOpenData\Collections\Combined();

// Retrieves the combined data in a multi dimensional array
$regions = $combined->get($cached = false);

// Everything is placed within collections for easy retrieving
$sudwestFryslan = $regions
    ->where('Name', 'Noord-Nederland')
        ->first()->get('Provinces')
    ->where('Name', 'Fryslân')
    ->first()->get('Municipalities')
    ->where('Name', 'Súdwest-Fryslân')
    ->first();


foreach ($regions as $regionKey => $region) {
    foreach ($region->get('Provinces') as $provinceKey => $province) {
        foreach ($province->get('Municipalities') as $municipalityKey => $municipality) {
           dd($municipality);
        }
    }
}