<?php


namespace CBSOpenData\Data;

use CBSOpenData\OpenData;
use CBSOpenData\OpenDataClient;
use CBSOpenData\Traits\UseFilesystem;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

abstract class AbstractData
{
    use UseFilesystem;

    public function get(bool $cache = false): Collection
    {
        if($cache) {
            $cache = $this->getCacheKey();
        }
        return $this->parseData((new OpenDataClient($this->getFilesystem()))->get($this->getOpenDataUrl(), $cache));
    }

    abstract protected function parseData(Collection $data): Collection;

    abstract protected function getCacheKey(): string;

    abstract protected function getOpenDataUrl(): string;
}