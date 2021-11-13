<?php
namespace TurboLabIt\TLIBaseBundle\Traits;


trait IdableEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $id;

    protected $acceptZero = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int|string $id)
    {
        $intId = (int)$id;
        if( $intId === 0 && $id != 0 ) {
            throw new \InvalidArgumentException();
        }

        if( $intId === 0 && !$this->acceptZero ) {
            throw new \ValueError();
        }


        if( $intId < 0 ) {
            throw new \ValueError();
        }

        $this->id = $intId;
        return $this;
    }

    public function acceptZero(bool $bool = true)
    {
        $this->acceptZero = $bool;
        return $this;
    }
}
