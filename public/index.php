<?php

require '../vendor/autoload.php';


//$data = new \CBSOpenData\Data();
//dd($data->get('residences', true));

$living = new \CBSOpenData\Combined\Living();
dd($living->get(true));
exit;