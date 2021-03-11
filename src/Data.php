<?php


namespace CBSOpenData;


use CBSOpenData\Data\DistrictsAndNeighbourhoods;
use CBSOpenData\Data\Regions;
use CBSOpenData\Data\Residences;
use CBSOpenData\Data\ResidencesTableInfos;
use Illuminate\Support\Collection;

class Data
{

    protected $dataTypes = [
        'regions' => Regions::class,
        'residences' => Residences::class,
        'residences_table_infos' => ResidencesTableInfos::class,
        'districts_and_neighbourhoods' => DistrictsAndNeighbourhoods::class
    ];

    /**
     * @param $type
     * @param false $cache
     * @return Collection
     */
    public function get($type, $cache = false): Collection
    {
        if(isset($this->dataTypes[$type])) {
            return (new $this->dataTypes[$type])->get($cache);
        }
    }
}