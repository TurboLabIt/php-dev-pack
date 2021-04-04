<?php
namespace TurboLabIt\TLIBaseBundle\tests\Traits;


use TurboLabIt\TLIBaseBundle\Traits\IdableEntity;
use TurboLabIt\TLIBaseBundle\Traits\NameableEntity;
use TurboLabIt\TLIBaseBundle\Traits\PublicableEntity;
use TurboLabIt\TLIBaseBundle\Traits\TitleableEntity;
use TurboLabIt\TLIBaseBundle\Traits\ViewableEntity;


class DummyEntity
{
    use IdableEntity, NameableEntity, TitleableEntity, PublicableEntity, ViewableEntity;
}
