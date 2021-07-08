<?php
/**
 * Generate the actual Stopwords class.
 *
 * How to:
 *
 *  1. edit the template at `/Stopwords/Stopwords.template.php`
 *  1. run `/Stopwords/GenerateClass.php`
 *  1. update the test in `/tests/Service/StopwordsTest.php`
 *
 * clear && php8.0 Stopwords/GenerateClass.php dev && ./vendor/bin/simple-phpunit
 *
 * Both the template and the generated file MUST be committed to the repo.
 */
title( basename(__FILE__, '.php') . " is running...");

$sourceDirectory    = realpath(__DIR__) . DIRECTORY_SEPARATOR;
$targetDirectory    = realpath($sourceDirectory . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Service') . DIRECTORY_SEPARATOR;
$devMode            = !empty($argv[1]) && $argv[1] == 'dev';

echo "Source: ##" . $sourceDirectory . "##" . PHP_EOL;
echo "Target: ##" .  $targetDirectory . "##" . PHP_EOL;
echo "Dev mode: " . ($devMode ? 'Yes' : 'No') . PHP_EOL . PHP_EOL;

$classContent = file_get_contents($sourceDirectory . 'Stopwords.template.php');

//
$stopwordsContent = file_get_contents($sourceDirectory . 'stopwords-it.txt');
$arrWordsOrigin   = explode(PHP_EOL, $stopwordsContent);

$arrWords = [];
foreach($arrWordsOrigin as $oneWord) {

    $stopwordToKeep = trim($oneWord);
    if( empty($stopwordToKeep) || strpos($stopwordToKeep, '#') === 0 ) {

        continue;
    }

    $stopwordToKeep = str_replace("'", "\\'", $stopwordToKeep);


    $arrWords[] = "'" . $stopwordToKeep . "'";
}

$csvStopwords = implode(', ', $arrWords);

//
$cautionBannerContent = file_get_contents($sourceDirectory . 'caution-banner.txt');
$cautionBannerContent = str_replace('## BUILD TIME ##', date('Y-d-m H:i:s') . ' on ' . gethostname(), $cautionBannerContent);
$classContent = str_replace('/*## CAUTION-BANNER ##*/', $cautionBannerContent, $classContent);

//
$classContent = str_replace('/*## LIST ##*/', $csvStopwords, $classContent);

//
$xdebugOrNull = $devMode ? '$breakPointMe = 1;' : null;
$classContent = str_replace('/*## XDEBUG_BREAK ##*/', $xdebugOrNull, $classContent);

//
file_put_contents($targetDirectory . 'Stopwords.php', $classContent);
echo file_get_contents($targetDirectory . 'Stopwords.php') . PHP_EOL;
title("Done");


function title($text)
{
    echo "\e[1;37;42m" . $text . "\e[0m\n" . PHP_EOL;
}