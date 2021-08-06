<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


abstract class PaginatorBase
{
    protected UrlGeneratorInterface $urlGenerator;

    // page numbers
    protected int $currentPageNum       = 0;
    protected int $totalElementsNum     = 0;
    protected int $elementsPerPageNum   = 0;

    // prev and next page
    protected array $arrPreviousPage    = [];
    protected array $arrCurrentPage     = [];
    protected array $arrNextPage        = [];


    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    protected function build(string $routeName, array $arrRouteParam = [], $routeWithNumName = null, $arrRouteWithNumParam = null): static
    {
        $routeWithNumName = $routeWithNumName ?? $routeName;
        $arrRouteWithNumParam = $arrRouteWithNumParam ?? $arrRouteParam;

        if( $this->currentPageNum == 1 ) {

            $prevPageUrl = null;
            $currPageUrl = $this->urlGenerator->generate($routeName, $arrRouteParam, UrlGeneratorInterface::ABSOLUTE_URL);

        } elseif( $this->currentPageNum == 2 ) {

            $prevPageUrl = $this->urlGenerator->generate($routeName, $arrRouteParam, true);
            $currPageUrl = $this->urlGenerator->generate($routeWithNumName, array_merge($arrRouteWithNumParam, ["pageNum" => $this->currentPageNum]), UrlGeneratorInterface::ABSOLUTE_URL);

        } else {

            $prevPageUrl = $this->urlGenerator->generate($routeWithNumName, array_merge($arrRouteWithNumParam, ["pageNum" => $this->currentPageNum - 1]), UrlGeneratorInterface::ABSOLUTE_URL);
            $currPageUrl = $this->urlGenerator->generate($routeWithNumName, array_merge($arrRouteWithNumParam, ["pageNum" => $this->currentPageNum]), UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $this->arrPreviousPage  = [ "url" => $prevPageUrl ];
        $this->arrCurrentPage   = [ "url" => $currPageUrl ];
        $this->arrNextPage      = [ "url" => $this->urlGenerator->generate($routeWithNumName, array_merge($arrRouteWithNumParam, ["pageNum" => $this->currentPageNum + 1]), UrlGeneratorInterface::ABSOLUTE_URL) ];

        return $this;
    }


    public function setCurrentPageNum(int $page): static
    {
        $this->currentPageNum = $page;
        return $this;
    }


    public function getCurrentPageNum(): int
    {
        return $this->currentPageNum;
    }


    public function setTotalElementsNum(int $num): static
    {
        $this->totalElementsNum = $num;
        return $this;
    }


    public function setElementsPerPageNum(int $num): static
    {
        $this->elementsPerPageNum = $num;
        return $this;
    }


    public function isPageOutOfRange(): int|bool
    {
        if( $this->currentPageNum > $this->getMaxPageNum() ) {

            return $this->getMaxPageNum();
        }

        return false;
    }


    public function getMaxPageNum(): int
    {
        $maxPage = $this->totalElementsNum / $this->elementsPerPageNum;
        $maxPage = $maxPage ?: 1;
        return (int)ceil($maxPage);
    }


    public function getPreviousPage(): ?array
    {
        if( $this->currentPageNum > 1 ) {

            return $this->arrPreviousPage;
        }

        return null;
    }


    public function getCurrentPage(): ?array
    {
        return $this->arrCurrentPage;
    }


    public function getNextPage(): ?array
    {
        if( $this->currentPageNum < $this->getMaxPageNum() ) {

            return $this->arrNextPage;
        }

        return null;
    }
}
