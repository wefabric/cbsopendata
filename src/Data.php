<?php


namespace CBSOpenData;


use CBSOpenData\Data\DistrictsAndNeighbourhoods;
use CBSOpenData\Data\Regions;
use CBSOpenData\Data\Residences;
use CBSOpenData\Data\ResidencesTableInfos;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Data
{

    protected $dataTypes = [
        'regions' => Regions::class,
        'residences' => Residences::class,
        'residences_table_infos' => ResidencesTableInfos::class,
        'districts_and_neighbourhoods' => DistrictsAndNeighbourhoods::class
    ];

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @param $type
     * @param false $cache
     * @return Collection
     */
    public function get($type, $cache = false): Collection
    {
        if(isset($this->dataTypes[$type])) {
            $dataTypeClass = (new $this->dataTypes[$type]);
            $dataTypeClass->setFileSystem($this->getFileSystem());
            return $dataTypeClass->get($cache);
        }
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem()
    {
        if(!$this->fileSystem) {
            $this->setFileSystem(new Filesystem(new Local(OpenData::cachePath())));
        }

        return $this->fileSystem;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFileSystem(Filesystem $filesystem): void
    {
        $this->fileSystem = $filesystem;
    }
}