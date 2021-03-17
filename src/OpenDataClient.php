<?php


namespace CBSOpenData;


use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;

class OpenDataClient
{

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->fileSystem = $filesystem;
    }

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
        $response = $client->get($url);
        $openData = json_decode( (string)$response->getBody(), true, $depth=512, JSON_THROW_ON_ERROR);
        if(isset($openData['value'])) {
            $result = collect($openData['value']);
            if($cache) {
                $this->fileSystem->put($cache.'.json', $result->toJson());
            }
        }
        return $result;
    }

    /**
     * @param string $cache
     * @return Collection
     */
    private function getFromCache(string $url, string $cache): Collection
    {
        try {
            $file = $this->fileSystem->get($cache.'.json');
            return collect(json_decode($file->read(), true));
        } catch (FileNotFoundException $e) {
            return $this->getFromUrl($url, $cache);
        }
    }
}