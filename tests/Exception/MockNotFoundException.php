<?php
namespace TurboLabIt\TLIBaseBundle\tests\Exception;

use TurboLabIt\TLIBaseBundle\Exception\NotFoundException;


class MockNotFoundException extends NotFoundException
{
    protected $category = 'test';
}
