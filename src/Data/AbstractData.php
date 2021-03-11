<?php


namespace CBSOpenData\Data;

use CBSOpenData\OpenDataClient;
use Illuminate\Support\Collection;

abstract class AbstractData
{
    public function get(bool $cache = false): Collection
    {
        if($cache) {
            $cache = $this->getCacheKey();
        }
        return $this->parseData((new OpenDataClient())->get($this->getOpenDataUrl(), $cache));
    }

    abstract protected function parseData(Collection $data): Collection;

    abstract protected function getCacheKey(): string;

    abstract protected function getOpenDataUrl(): string;
}