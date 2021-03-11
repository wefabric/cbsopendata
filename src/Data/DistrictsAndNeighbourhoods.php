<?php


namespace CBSOpenData\Data;

use Illuminate\Support\Collection;

class DistrictsAndNeighbourhoods extends AbstractData
{
    const OPENDATA_URL = 'https://opendata.cbs.nl/ODataApi/odata/84799NED/WijkenEnBuurten';

    const CACHE_KEY = 'opendata_districts_and_neighbourhoods';

    protected function parseData(Collection $data): Collection
    {
        $result = new Collection();
        foreach ($data as $key => $item) {
            $districtOrNeighbourhood = new Collection($item);
            $districtOrNeighbourhood['Key'] = rtrim($districtOrNeighbourhood['Key']);
            $districtOrNeighbourhood['Title'] = rtrim($districtOrNeighbourhood['Title']);
            $result->put($key, $districtOrNeighbourhood);
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