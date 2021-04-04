<?php
namespace TurboLabIt\TLIBaseBundle\Traits;


trait ViewableEntity
{
    /**
     * @ORM\Column(type="integer", options={"default" : 0, "unsigned": true})
     */
    private $views = 0;

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int|string $views): self
    {
        $intViews = (int)$views;
        if( $intViews === 0 && $views != 0 ) {

            throw new \InvalidArgumentException();
        }

        if( $intViews < 0 ) {

            throw new \ValueError();
        }

        $this->views = $intViews;
        return $this;
    }

    /**
     * You'll probably want to use
     *
     *   TurboLabIt\TLIBaseBundle\Traits\AtomicFieldIncrease
     *
     * For the repository of your entity
     */
}
