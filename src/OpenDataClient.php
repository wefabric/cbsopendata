<?php


namespace CBSOpenData;


use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class OpenDataClient
{

    /**
     * @param string $url
     * @param string $cache
     * @return Collection
     */
    public function get(string $url, string $cache = ''): Collection
    {
        if($cache) {
            return $this->getFromCache($url, $cache) ?? $this->getFromUrl($url, $cache);
        }
        return $this->getFromUrl($url, $cache);

    }

    private function getFromUrl(string $url, string $cache = ''): Collection
    {
        $client = new Client();
        $result = new Collection();
        try {
            $response = $client->get($url);
            $openData = json_decode( (string)$response->getBody(), true, $depth=512, JSON_THROW_ON_ERROR);
            if(isset($openData['value'])) {
                $result = collect($openData['value']);
                if($cache) {
                    file_put_contents($cache.'.json', $result->toJson());
                }
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
        return $result;
    }

    /**
     * @param string $cache
     * @return Collection
     */
    private function getFromCache(string $url, string $cache): Collection
    {
        if(!file_exists($cache.'.json')) {
            return $this->getFromUrl($url, $cache);
        }
        return collect(json_decode(file_get_contents($cache.'.json'), true));
    }
}