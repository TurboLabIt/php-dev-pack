<?php
namespace TurboLabIt\TLIBaseBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    protected ObjectManager $manager;

    /** @var Generator */
    protected Generator $faker;
    
    protected array $arrGeneratedCodes = [];

    abstract protected function loadData(ObjectManager $manager);


    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $this->loadData($manager);
    }


    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);

            $this->manager->persist($entity);
            // store for usage later as App\Entity\ClassName_#COUNT#
            $this->addReference($className . '_' . $i, $entity);
        }

        $this->manager->flush();
    }
    
    
    protected function generateUniqueCode($pattern = '?##??##?')
    {
        do {

            $code = $this->faker->bothify($pattern);

        } while( in_array($code, $this->arrGeneratedCodes));

        $this->arrGeneratedCodes[] = $code;
        
        return $code;
    }
}
