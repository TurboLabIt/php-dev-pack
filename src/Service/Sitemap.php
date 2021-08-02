<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TurboLabIt\TLIBaseBundle\Service\FileType\Xml;
use Twig\Environment;


class Sitemap
{
    const FORMAT_GENERIC    = 'generic';
    const FORMAT_NEWS       = 'news';
    const URL_MAX_PER_FILE  = 10;

    protected string $outputPath;
    protected Environment $twig;
    protected Xml $xmlFileType;
    protected Filesystem $filesystem;
    protected HttpClientInterface $httpClient;

    protected string $sitemapBaseUrl;
    protected string $indexFileUrl;

    protected array $arrFilesForIndex = [];
    protected array $arrUrlsByFormatAndName = [];


    public function __construct(ParameterBagInterface $parameterBag, Environment $twig, Xml $xmlFileType, Filesystem $filesystem, HttpClientInterface $httpClient)
    {
        $this->outputPath   = $parameterBag->get('kernel.project_dir') .
                                DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'sitemap' . DIRECTORY_SEPARATOR;

        $this->twig         = $twig;
        $this->xmlFileType  = $xmlFileType;
        $this->filesystem   = $filesystem;
        $this->httpClient   = $httpClient;

        $this->createDirectoryTemporary(true);
    }


    public function setBaseUrl(string $sitemapBaseUrl): self
    {
        $sitemapBaseUrl .= str_ends_with($sitemapBaseUrl, '/') ? '' : '/';
        $this->sitemapBaseUrl = $sitemapBaseUrl;
        return $this;
    }


    public function addEntry(array $arrOneUrl, string $fileNameNoExt): self
    {
        $format = empty($format) ? static::FORMAT_GENERIC : $format;
        $this->arrUrlsByFormatAndName[$format][$fileNameNoExt][] = $arrOneUrl;
        return $this;
    }


    public function writeOneFile(string $fileNameNoExt, string $format = null, bool $clearData = true): array
    {
        $format  = empty($format) ? static::FORMAT_GENERIC : $format;
        $arrData = $this->arrUrlsByFormatAndName[$format][$fileNameNoExt];

        if($clearData) {
            unset($this->arrUrlsByFormatAndName[$format][$fileNameNoExt]);
        }

        $arrDataSplitForFiles = $this->splitDataForFileLimit($arrData, $fileNameNoExt);
        foreach($arrDataSplitForFiles as $oneFileName => $oneFileData) {

            $this->arrFilesForIndex[] = $this->writeXmlDataToFile($format, ["urlData" => $oneFileData], $oneFileName);
        }

        return array_keys($arrDataSplitForFiles);
    }


    public function writeIndex(): string
    {
        $arrFiles = [];
        foreach($this->arrFilesForIndex as $filePath) {

            $arrFiles[] = $this->sitemapBaseUrl . basename($filePath);
        }

        $indexFile = $this->writeXmlDataToFile('index', ["Urls" => $arrFiles], 'sitemap.xml');
        $this->indexFileUrl = $this->sitemapBaseUrl . basename($indexFile);
        return $this->indexFileUrl;
    }


    public function notify(): array
    {
        $arrReport = [];

        $sitemapUrlEncoded = urlencode($this->indexFileUrl);

        $arrUrlsToNotify = [
            "https://www.google.com/ping?sitemap=" . $sitemapUrlEncoded,
            "http://www.bing.com/ping?sitemap=" . $sitemapUrlEncoded
        ];

        foreach($arrUrlsToNotify as $oneUrlToPing) {

            $response   = $this->httpClient->request('GET', $oneUrlToPing);
            $statusCode = $response->getStatusCode();

            if( empty($statusCode) || $statusCode < 200 || $statusCode > 299) {
                throw new \Exception("Notify error! Status code ##" . $statusCode . "## from " . $oneUrlToPing);
            }

            $arrReport[$oneUrlToPing] = [
                "statusCode"    => $statusCode,
                "text"          => $response->getContent()
            ];
        }

        return $arrReport;
    }


    protected function writeXmlDataToFile($format, $arrTemplateData, $filename)
    {
        $xmlTemplateFile = $this->getTemplateFromFormat($format);

        $xml = $this->twig->render($xmlTemplateFile, $arrTemplateData);
        $xml = $this->xmlFileType->prettify($xml);

        $fullFileName   = $this->createDirectoryTemporary();
        $fullFileName  .= str_starts_with($filename, 'sitemap')
                            ? $filename : ('sitemap_' . $filename);

        file_put_contents($fullFileName, $xml);

        $fullFileNameGz = $fullFileName . '.gz';

        $gzFile = gzopen($fullFileNameGz, 'w9');
        gzwrite($gzFile, $xml);
        gzclose($gzFile);

        return $fullFileNameGz;
    }


    protected function splitDataForFileLimit($arrData, $fileNameNoExt): array
    {
        $arrFiles = [];
        $lastFileNum = 1;

        foreach($arrData as $arrOneUrl) {

            if($lastFileNum == 1) {

                $dataIdx = $fileNameNoExt . ".xml";

            } else {

                $fileNumSuffix = $lastFileNum <= 9 ? ('0' . $lastFileNum) : $lastFileNum;
                $dataIdx = $fileNameNoExt . "_" . $fileNumSuffix . ".xml";
            }
            
            $arrFiles[$dataIdx][] = $arrOneUrl;

            if( count($arrFiles[$dataIdx]) >= static::URL_MAX_PER_FILE ) {
                $lastFileNum++;
            }
        }

        return $arrFiles;
    }


    protected function createDirectoryTemporary(bool $removeIfExists = false): string
    {
        if( $removeIfExists && $this->filesystem->exists($this->outputPath) ) {

            $this->filesystem->remove($this->outputPath);

        } elseif( !$removeIfExists && $this->filesystem->exists($this->outputPath) ) {

            return $this->outputPath;
        }

        $this->filesystem->mkdir($this->outputPath);

        return $this->outputPath;
    }


    protected function getTemplateFromFormat($format): string
    {
        return '@TLIBase/sitemap/' . $format . '.xml.twig';
    }
}