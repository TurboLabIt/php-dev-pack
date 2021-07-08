<?php
/*## CAUTION-BANNER ##*/
namespace TurboLabIt\TLIBaseBundle\Service;


class Stopwords
{
    const LIST_IT = [/*## LIST ##*/];


    public function clean(string $text)
    {
        $text = trim($text);
        foreach(self::LIST_IT as $stopword) {

            $regex = '/\b' . $stopword . '\'\b/iu';
            $textClean = preg_replace($regex, '', $text);

            if( $textClean != $text) {
                /*## XDEBUG_BREAK ##*/
            }

            $text = $textClean;

            $regex = '/\b' . $stopword . '\b/iu';
            $textClean = preg_replace($regex, '', $text);

            if( $textClean != $text) {
                /*## XDEBUG_BREAK ##*/
            }

            $text = $textClean;
        }

        // remove double spaces
        $text = trim($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return $text;
    }
}
