<?php
namespace TurboLabIt\TLIBaseBundle\Service\Video\YouTube\Api;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TurboLabIt\TLIBaseBundle\Exception\YouTubeException;
use TurboLabIt\TLIBaseBundle\Service\Video\Video;


class YouTubeChannelApi
{
    protected $apiEndpoint = "https://youtube.googleapis.com/youtube/v3/";


    public function __construct(
        protected array $arrConfig,
        protected HttpClientInterface $httpClient,
        protected AdapterInterface $cache,
        protected YouTubeException $exception
    )
    {

    }


    public function getLatestVideos(int $results = 5): ?array
    {
        $cacheKey   = "youtube_latest-videos_" . $this->arrConfig["channelId"]  ."_" . $results;
        $value = $this->cache->get($cacheKey, function (ItemInterface $item) use($results) {

            $response = $this->getLatestVideosUncached($results);

            if( empty($response) ) {

                $item->expiresAfter(1);

            } else {

                $item->expiresAfter($this->arrConfig["latestCacheMinutes"] * 60);
            }

            return $response;
        });

        return $value;
    }


    public function getLatestVideosUncached(int $results = 5): ?array
    {
        $apiEndpoint = $this->apiEndpoint . "search";

        $arrParams   = [
            "part"          => "snippet",
            "channelId"     => $this->arrConfig["channelId"],
            "key"           => $this->arrConfig["apiKey"],
            "maxResults"    => $results,
            "order"         => "date"
        ];

        $response = $this->httpClient->request('GET', $apiEndpoint, [
            'query'     => $arrParams,
            'timeout'   => 15
        ]);


        try {

            if( $response->getStatusCode() != Response::HTTP_OK ) {
                throw $this->exception;
            }

        } catch (\Exception $ex) {
            throw $this->exception;
        }


        $txtResponse = $response->getContent();
        $objResponse = json_decode($txtResponse);

        if( empty($objResponse) || empty($objResponse->items) ) {
            throw $this->exception;
        }

        $arrVideos = [];
        foreach($objResponse->items as $oneVideoItem) {
            $arrVideos[] = (new Video())->loadFromYouTubeApiResponse($oneVideoItem);
        }

        return $arrVideos;
    }
}
