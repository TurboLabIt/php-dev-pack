<?php
namespace TurboLabIt\TLIBaseBundle\Service\Video\YouTube\Api;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class YouTubeChannelApiCached extends YouTubeChannelApi
{
    protected AdapterInterface $cache;


    public function __construct($arrConfig, HttpClientInterface $httpClient, AdapterInterface $cache)
    {
        parent::__construct($arrConfig, $httpClient);
        $this->cache        = $cache;
    }


    public function getLatestVideos(int $results = 5): ?array
    {
        $cacheKey   = "youtube_latest-videos_" . $this->arrConfig["channelId"]  ."_" . $results;
        $value = $this->cache->get($cacheKey, function (ItemInterface $item) use($results) {

            $response = parent::getLatestVideos($results);

            if( empty($response) ) {

                $item->expiresAfter(1);

            } else {

                $item->expiresAfter($this->arrConfig["latestCacheMinutes"] * 60);
            }

            return $response;
        });

        return $value;
    }
}
