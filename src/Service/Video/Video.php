<?php
namespace TurboLabIt\TLIBaseBundle\Service\Video;

use TurboLabIt\TLIBaseBundle\Exception\UnhandledVideoUrl;
use TurboLabIt\TLIBaseBundle\Exception\UndefinedMagicMethodException;


class Video
{
    const MEDIASOURCE_YOUTUBE   = 'youtube';
    const MEDIASOURCE_VIMEO     = 'vimeo';

    protected array $arrRegExUrl;
    protected array $arrData = [];


    public function __construct()
    {
        $this->arrRegExUrl = [
            static::MEDIASOURCE_YOUTUBE => 'http(?:s?):\/\/(?:www\.)?youtu(?:be\.com\/watch\?v=|\.be\/)([\w\-\_]*)(&(amp;)?[\w\?‌​=]*)?',
            static::MEDIASOURCE_VIMEO   => '(http|https)?:\/\/(www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|)(\d+)(?:|\/\?)'
        ];
    }


    public function loadFromUrl(string $videoUrl): static
    {
        $arrMatches = [];
        if( preg_match('/' . $this->arrRegExUrl[static::MEDIASOURCE_YOUTUBE] . '/',  $videoUrl, $arrMatches) && !empty($arrMatches[1]) ) {

            $this->arrData = [
                "id"        => $arrMatches[1],
                "source"    => self::MEDIASOURCE_YOUTUBE,
                "url"       => $videoUrl
            ];

        } elseif( preg_match('/' . $this->arrRegExUrl[static::MEDIASOURCE_VIMEO] . '/',  $videoUrl, $arrMatches) && !empty($arrMatches[4]) ) {

            $this->arrData = [
                "id"        => $arrMatches[4],
                "source"    => self::MEDIASOURCE_VIMEO,
                "url"       => $videoUrl
            ];

        } else {

            throw new UnhandledVideoUrl();
        }
        
        return $this;
    }


    public function loadFromYouTubeApiResponse($videoItem): static
    {
        $this->arrData["id"]            = $videoItem->id->videoId;
        $this->arrData["source"]        = self::MEDIASOURCE_YOUTUBE;
        $this->arrData["url"]           = "https://www.youtube.com/watch?v=" . $videoItem->id->videoId;
        $this->arrData["title"]         = ucfirst(mb_strtolower(trim($videoItem->snippet->title)));
        $this->arrData["abstract"]      = trim($videoItem->snippet->description);
        $this->arrData["thumbnails"]    = $videoItem->snippet->thumbnails;
        $this->arrData["publishedAt"]   = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $videoItem->snippet->publishedAt, new \DateTimeZone('UTC'))
                                            ->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $this;
    }


    public function getAsArray(array $options = []): array
    {
        return $this->arrData;
    }


    public function __call(string $name, array $arguments)
    {
        if( array_key_exists($name, $this->arrData) ) {

            return $this->arrData[$name];
        }

        if( stripos($name, 'get') === 0 ) {

            $idx = lcfirst(substr($name, 3));
            if( array_key_exists($idx, $this->arrData) ) {

                return $this->arrData[$name];
            }
        }

        // handling a set
        if( stripos($name, 'set') === 0 ) {

            $idx = lcfirst(substr($name, 3));
            $this->arrData[$idx] = reset($arguments);
            return $this;
        }

        // if the key still doesn't exists => throw a specific exception to notify the developer
        throw new UndefinedMagicMethodException($name);
    }
}
