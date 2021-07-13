<?php
namespace TurboLabIt\TLIBaseBundle\Traits;


trait NameableEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = trim($name);
        return $this;
    }
}
