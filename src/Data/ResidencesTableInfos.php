<?php


namespace CBSOpenData\Data;

use Illuminate\Support\Collection;

class ResidencesTableInfos extends AbstractData
{
    const OPENDATA_URL = 'https://opendata.cbs.nl/ODataApi/odata/84734NED/TypedDataSet';

    const CACHE_KEY = 'opendata_residences_table_infos';

    protected function parseData(Collection $data): Collection
    {
        $result = new Collection();
        foreach ($data as $key => $item) {
            $itemCollection = new Collection();

            $itemCollection->put('ID', $item['ID']);

            if(isset($item['Woonplaatsen'], $item['Woonplaatscode_1'])) {
                $residence = [
                    'Name' => rtrim($item['Woonplaatsen']),
                    'Code' => rtrim($item['Woonplaatscode_1'])
                ];

                $itemCollection->put('Residence', $residence);
            }

            if(isset($item['Naam_2'], $item['Code_3'])) {
                $municipality = [
                    'Name' => rtrim($item['Naam_2']),
                    'Code' => rtrim($item['Code_3'])
                ];

                $itemCollection->put('Municipality', $municipality);
            }

            if(isset($item['Naam_4'], $item['Code_5'])) {
                $province = [
                    'Name' => rtrim($item['Naam_4']),
                    'Code' => rtrim($item['Code_5'])
                ];

                $itemCollection->put('Province', $province);
            }

            if(isset($item['Naam_6'], $item['Code_7'])) {
                $countryPart = [
                    'Name' => rtrim($item['Naam_6']),
                    'Code' => rtrim($item['Code_7'])
                ];

                $itemCollection->put('CountryPart', $countryPart);
            }

            $result->put($key, $itemCollection);
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