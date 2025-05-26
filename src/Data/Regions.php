<?php

namespace CBSOpenData\Data;

use Illuminate\Support\Collection;

class Regions extends AbstractData
{
    const OPENDATA_URL = 'https://opendata.cbs.nl/ODataApi/odata/86097NED/RegioS';

    const CACHE_KEY = 'opendata_regions';

    protected function parseData(Collection $data): Collection
    {
        $result = new Collection();
        foreach ($data as $key => $item) {
            $region = new Collection($item);
            $region['Key'] = rtrim($region['Key']);
            $region['Title'] = rtrim($region['Title']);
            $result->put($key, $region);
        }
        return $result;
    }

    protected function getCacheKey(): string
    {
        return self::CACHE_KEY;
    }

    protected function getOpenDataUrl(): string
    {
        return self::OPENDATA_URL;
    }
}
