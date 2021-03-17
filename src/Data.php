<?php


namespace CBSOpenData;


use CBSOpenData\Data\DistrictsAndNeighbourhoods;
use CBSOpenData\Data\Regions;
use CBSOpenData\Data\Residences;
use CBSOpenData\Data\ResidencesTableInfos;
use CBSOpenData\Traits\UseFilesystem;
use Illuminate\Support\Collection;

class Data
{
    use UseFilesystem;

    /**
     * @var string[]
     */
    protected $dataTypes = [
        'regions' => Regions::class,
        'residences' => Residences::class,
        'residences_table_infos' => ResidencesTableInfos::class,
        'districts_and_neighbourhoods' => DistrictsAndNeighbourhoods::class
    ];

    /**
     * @param false $cache
     * @return Collection
     */
    public function getRegions($cache = false): Collection
    {
        return $this->get('regions', $cache);
    }

    /**
     * @param false $cache
     * @return Collection
     */
    public function getResidences($cache = false): Collection
    {
        return $this->get('residences', $cache);
    }

    /**
     * @param false $cache
     * @return Collection
     */
    public function getResidencesTableInfo($cache = false): Collection
    {
        return $this->get('residences_table_infos', $cache);
    }

    /**
     * @param false $cache
     * @return Collection
     */
    public function getDistrictsAndNeighbourhoods($cache = false): Collection
    {
        return $this->get('districts_and_neighbourhoods', $cache);
    }

    /**
     * @param $type
     * @param false $cache
     * @return Collection
     */
    public function get($type, $cache = false): Collection
    {
        if(isset($this->dataTypes[$type])) {
            $dataTypeClass = (new $this->dataTypes[$type]);
            $dataTypeClass->setFilesystem($this->getFilesystem());
            return $dataTypeClass->get($cache);
        }
    }


}