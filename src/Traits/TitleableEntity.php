<?php
namespace TurboLabIt\TLIBaseBundle\Traits;

/**
 * Do you need it unique?
 * @see https://github.com/TurboLabIt/TraitsBundle/blob/master/README.md#%EF%B8%8F-traits-ableentity
 */


trait TitleableEntity
{
    /**
     * @ORM\Column(type="string", length=512)
     */
    private $title;
    
    private $autoclean = true;

    public function getTitle(): ?string
    {
        return
            $this->clean($this->title);
    }

    public function setTitle(string $title): self
    {
        $this->title = $this->clean($title);
        return $this;
    }
    
    protected function clean(string $text): string
    {
        if(!$this->autoclean) {
         
            return $text;
        }
        
        return 
            trim(strip_tags($text));
    }
    
    protected function disableAutoclean(bool $disable = true)
    {
        $this->autoclean = !$disable;
        return $this;
    }
}
