<?php

require '../vendor/autoload.php';



//$data = new \CBSOpenData\Data();
//dd($data->get('residences', true));
set_time_limit(0); // safe_mode is off
ini_set('max_execution_time', 0); //500 seconds
$living = new \CBSOpenData\Collections\Living();
$regions = $living->get(true);


$sudwestFryslan = $regions->where('Name', 'Noord-Nederland')->first()['Provinces']->where('Name', 'Fryslân')->first()->get('Municipalities')->where('Name', 'Súdwest-Fryslân')->first();
dd($sudwestFryslan);
foreach ($regions as $regionKey => $region) {
    foreach ($region->get('Provinces') as $provinceKey => $province) {
        foreach ($province->get('Municipalities') as $municipalityKey => $municipality) {
           dd($municipality);
        }
    }
}
exit;