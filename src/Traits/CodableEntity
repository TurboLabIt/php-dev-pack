<?php
namespace TurboLabIt\TLIBaseBundle\Traits;


trait CodableEntity
{
    /**
     * @ORM\Column(type="string", length=35, unique=true)
     */
    private $code;
    
    protected bool autoCapitalize = true;
    
    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {    
        $this->code = $this->autoCapitalize ? mb_strtoupper($code) : $code;
        return $this;
    }
    
    public function autoCapitalize(bool $bool = true): self
    {
        $this->autoCapitalize = $bool;
        return $this;
    }
}
