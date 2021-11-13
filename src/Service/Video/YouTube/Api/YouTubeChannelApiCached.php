<?php
namespace TurboLabIt\TLIBaseBundle\Service\Video\YouTube\Api;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;


class YouTubeChannelApiCached extends YouTubeChannelApi
{
    public function __construct(
        protected YouTubeChannelApi $api,
        protected AdapterInterface $cache
    ) {
    }


    public function getLatestVideos(int $results = 5): ?array
    {
        $cacheKey   = "youtube_latest-videos_" . $this->arrConfig["channelId"]  ."_" . $results;
        $value = $this->cache->get($cacheKey, function (ItemInterface $item) use($results) {

            $response = $this->api->getLatestVideos($results);

            if( empty($response) ) {

                $item->expiresAfter(1);

            } else {

                $item->expiresAfter($this->arrConfig["latestCacheMinutes"] * 30);
            }

            return $response;
        });

        return $value;
    }
}
