<?php


namespace CBSOpenData\Data;

use CBSOpenData\OpenData;
use CBSOpenData\OpenDataClient;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

abstract class AbstractData
{
    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function get(bool $cache = false): Collection
    {
        if($cache) {
            $cache = $this->getCacheKey();
        }
        return $this->parseData((new OpenDataClient($this->getFileSystem()))->get($this->getOpenDataUrl(), $cache));
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

    abstract protected function parseData(Collection $data): Collection;

    abstract protected function getCacheKey(): string;

    abstract protected function getOpenDataUrl(): string;
}