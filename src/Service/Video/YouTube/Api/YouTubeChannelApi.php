<?php
namespace TurboLabIt\TLIBaseBundle\Service\Video\YouTube\Api;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TurboLabIt\TLIBaseBundle\Service\Video\Video;


class YouTubeChannelApi
{
    protected $apiEndpoint = "https://youtube.googleapis.com/youtube/v3/";


    public function __construct(
        protected array $arrConfig,
        protected HttpClientInterface $httpClient
    ) {

    }


    public function getLatestVideos(int $results = 5): ?array
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

                return null;
            }

        } catch (\Exception $ex) {

            return null;
        }


        $txtResponse = $response->getContent();
        $objResponse = json_decode($txtResponse);

        if( empty($objResponse) || empty($objResponse->items) ) {

            return null;
        }

        $arrVideos = [];
        foreach($objResponse->items as $oneVideoItem) {

            $arrVideos[] = (new Video())->loadFromYouTubeApiResponse($oneVideoItem);
        }

        return $arrVideos;
    }
}
