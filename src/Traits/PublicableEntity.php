<?php
namespace TurboLabIt\TLIBaseBundle\Traits;


trait PublicableEntity
{
    /**
     * @ORM\Column(type="boolean")
     */
    private $public = false;

    public function isPublic(): bool
    {
        return $this->public === true;
    }

    public function isPrivate(): bool
    {
        return !$this->isPublic();
    }

    public function setPublic(bool|string|int $value = true)
    {
        if( is_string($value) && !in_array($value, ["0","1"]) ) {

            throw new \ValueError();
        }

        $this->public = (bool)$value;
        return $this;
    }

    public function setPrivate(bool|string|int $value = true)
    {
        $this->setPublic($value);
        $this->public = !$this->public;
        return $this;
    }
}
