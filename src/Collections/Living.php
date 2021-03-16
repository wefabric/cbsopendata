<?php


namespace CBSOpenData\Collections;


use CBSOpenData\Data;
use CBSOpenData\OpenDataClient;
use Illuminate\Support\Collection;

class Living
{
    const CACHE_KEY = 'living_data';

    /**
     * @param false $cache
     * @return Collection
     */
    public function get($cache = false): Collection
    {
        if($cache) {
            return $this->getFromCache(self::CACHE_KEY);
        }
        return $this->getFromOpenData($cache);
    }

    /**
     * @param string $cache
     * @return Collection
     */
    private function getFromCache(string $cache): Collection
    {
        \Illuminate\Support\Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });

        if(!file_exists($cache.'.json')) {
            return $this->getFromOpenData($cache);
        }
        return collect(json_decode(file_get_contents($cache.'.json'), true))->recursive();
    }

    /**
     * @param false $cache
     * @return Collection
     */
    private function getFromOpenData(string $cache = ''): Collection
    {
        $baseDataCache = $cache;
        if($cache) {
            $baseDataCache = 'living_base_data';
        }
        if($result = $this->getBaseData($baseDataCache)) {
            $result =  $this->getWithResidences($result);
            if($result) {
                file_put_contents(self::CACHE_KEY.'.json', $result->toJson());
            }
        }
        return $result;
    }

    public function getWithResidences($regions): Collection
    {
        $dataClass = new Data();
        $districtsAndNeighbourhoods = $dataClass->get('districts_and_neighbourhoods');
        foreach ($regions as $regionKey => $region) {
            foreach ($region->get('Provinces') as $provinceKey => $province) {
                foreach ($province->get('Municipalities') as $municipalityKey => $municipality) {

//                    if($municipality->get('Name') !== 'Súdwest-Fryslân') {
//                        continue;
//                    }

                    $districtsAndNeighbourhoodsOfMunicipality = $districtsAndNeighbourhoods->where('Municipality', $municipality->get('Code'))->all();
                    $municipality->put('Residences', $this->splitResidencesFromDistrictsAndNeighbourhoods($districtsAndNeighbourhoodsOfMunicipality, $municipality));
                    $regions->get($regionKey)->get('Provinces')->get($provinceKey)->get('Municipalities')->put($municipalityKey, $municipality);
                }
            }
        }
        return $regions;
    }

    private function getBaseData(string $cache = '')
    {
        $dataClass = new Data();
        $residencesTableInfos = $dataClass->get('residences_table_infos');
        $residences = $dataClass->get('residences');

        $living = new Collection();
        $count = 0;
        foreach ($residencesTableInfos as $residencesTableInfo) {
            $count++;
            if(!$countryPart = $living->where('Code', $residencesTableInfo->get('CountryPart')['Code'])->first()) {
                $countryPart = new Collection();
                $countryPart->put('Code', $residencesTableInfo->get('CountryPart')['Code']);
                $countryPart->put('Name', $residencesTableInfo->get('CountryPart')['Name']);
                $countryPart->put('Provinces', new Collection());
            }
            $countryPartKey = $living->where('Code', $residencesTableInfo->get('CountryPart')['Code'])->keys()->first();

            if(!$province = $countryPart->get('Provinces')->where('Code', $residencesTableInfo->get('Province')['Code'])->first()) {
                $province = new Collection($residencesTableInfo->get('Province'));
                $province->put('Municipalities', new Collection());
                $countryPart->get('Provinces')->push($province);
            }
            $provinceKey = $countryPart->get('Provinces')->where('Code', $residencesTableInfo->get('Province')['Code'])->keys()->first();

            if(!$municipality = $province->get('Municipalities')->where('Code', $residencesTableInfo->get('Municipality')['Code'])->first()) {
                $municipality = new Collection($residencesTableInfo->get('Municipality'));
                $municipality->put('Residences', new Collection());
                $province->get('Municipalities')->push($municipality);
            }

            if(!$residence = $municipality->get('Residences')->where('Code', $residencesTableInfo->get('Residence')['Code'])->first()) {
                if($residenceItem = $residences->where('Key', $residencesTableInfo->get('Residence')['Code'])->first()) {
                    $residenceItem = new Collection($residenceItem);
                    $residenceItem->put('Districts', new Collection());
                    $municipality->get('Residences')->push($residenceItem);
                }
            }

            $municipalityKey = $province->get('Municipalities')->where('Code', $residencesTableInfo->get('Municipality')['Code'])->keys()->first();
            $municipalities = $province->get('Municipalities');
            $municipalities->put($municipalityKey, $municipality);
            $province->put('Municipalities', $municipalities);

            $provinces = $countryPart->get('Provinces');
            $provinces->put($provinceKey, $province);
            $countryPart->put('Provinces', $provinces);

            $living->put($countryPartKey, $countryPart);
        }
        if($cache) {
            file_put_contents($cache.'.json', $living->toJson());
        }
        return $living;
    }


    private function splitResidencesFromDistrictsAndNeighbourhoods($districtsAndNeighbourhoodsOfMunicipality, $municipality)
    {
        $residences = new Collection();
        $residencesData = $municipality->get('Residences');

        $rawResidences = (new Data())->get('residences');

        foreach ($districtsAndNeighbourhoodsOfMunicipality as $item) {

            if(isset($item['Description']) && $item['Description']) {


                $strippedDescription = rtrim(str_replace('('.$item['Municipality'].')', '', $item['Description']));
                preg_match_all('/(Gemeente) \'(.*)\'/', $strippedDescription, $matches);
                if(isset($matches[1], $matches[1][0], $matches[2], $matches[2][0]) && $matches[1][0] === 'Gemeente') {

                    if(!$residenceDataItem = $residencesData->where('Title', $matches[2][0])->first()) {
                        continue;
                    }
                    if(!isset($residenceDataItem['Code'])) {
                        $residenceDataItem['Code'] = rtrim($item['Key']);
                    }
                    if(!$residence = $residences->where('Code', $residenceDataItem['Code'])->first()) {
                        $residence = new Collection($residenceDataItem);
                        $residence->put('Districts', new Collection());
                        $residences->push($residence);
                    }
                    continue;
                }

                if(substr($item['Description'], 0, 4) == 'Wijk') {
                    preg_match_all('/is een wijk in de gemeente \'(.*)\'/', $strippedDescription, $matches);
                    if(isset($matches[1], $matches[1][0])) {
                        if($residence = $residences->where('Title',$matches[1][0])->first()) {
                            $residenceKey = $residences->where('Title', $matches[1][0])->keys()->first();
                            $district = new Collection($item);
                            $district->put('Neighbourhoods', new Collection());
                            $residence->get('Districts')->push($district);
                            $residences->put($residenceKey, $residence);

                        }
                    }
                    continue;
                }

                if(substr($item['Description'], 0, 5) == 'Buurt') {


                    preg_match_all('/in de gemeente \'(.*)\'/', $strippedDescription, $residenceMatches);
                    if(isset($residenceMatches[0], $residenceMatches[0][0])) {

                        $strippedDescription = str_replace($residenceMatches[0][0], '', $strippedDescription);
                        preg_match_all('/is een buurt in wijk \'(.*)\'/', $strippedDescription, $matches);

                        if(isset($matches[1], $matches[1][0])) {

                            // If it is not a match, probably a merged Municipality
                            if(!$residence = $residences->where('Title', $matches[1][0])->first()) {

                                // CBS uses 'Wijk 10 NAME', which could be either a residence name or a district
                                preg_match_all('/Wijk\s\d*\s(.*)/', $matches[1][0], $residenceMatches);

                                if($residence = $residences->where('Title', $residenceMatches[1][0])->first()) {
                                    $residenceKey = $residences->where('Title', $residenceMatches[1][0])->keys()->first();
                                    $residence->get('Districts')->push($item);
                                    $residences->put($residenceKey, $residence);
                                } else {
                                    if($residence = $rawResidences->where('Title', $residenceMatches[1][0])->first()) {
                                        $residence->put('Districts', new Collection());
                                        $residence->get('Districts')->push($item);
                                        $residences->push($residence);
                                    }
                                }
                                continue;
                            }

                            if($residence) {
                                if(!$residence->get('Districts')->where('Title', $matches[1][0])->first()) {
                                    $residenceKey = $residences->where('Title', $residenceMatches[1][0])->keys()->first();
                                    $district = new Collection($item);
                                    $district->put('Neighbourhoods', new Collection());
                                    $residence->get('Districts')->push($district);
                                    $residences->put($residenceKey, $residence);
                                } else {
                                    $districtKey = $residences->where('Title', $residenceMatches[1][0])->first()->get('Districts')->where('Title', $matches[1][0])->keys()->first();
                                    if(!$district->get('Neighbourhoods')) {
                                        $district->put('Neighbourhoods', new Collection());
                                    }

                                    $district->get('Neighbourhoods')->push(new Collection($item));
                                    $residences->where('Title', $residenceMatches[1][0])->first()->get('Districts')->put($districtKey, $district);
                                }
                            }
                        }
                    }
                    continue;
                }


            }
        }
        return $residences;
    }
}