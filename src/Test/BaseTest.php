<?php
namespace TurboLabIt\TLIBaseBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BaseTest extends WebTestCase
{
    protected static $entityName;
    protected static $serviceName;
    protected static $webDomain = 'localhost';
  
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    
    protected $httpClient;


    protected function setUp(): void
    {
        $this->httpClient    = static::createClient();
        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    
    public function testInstance(): void
    {
        $instance = $this->getService();
        $this->assertInstanceOf(static::$serviceName, $instance);
    }


    protected function getService()
    {
        return static::getContainer()->get(static::$serviceName);
    }


    protected function getRandomRecord($entityName = null)
    {
        $entityName = $entityName ?: static::$entityName;
        
        return
            $this->entityManager->getRepository($entityName)
                ->createQueryBuilder('t')
                    ->orderBy('RAND()')
                    ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    
    protected function countRecord($entityName = null)
    {
        $entityName = $entityName ?: static::$entityName;
        
        return
            $this->entityManager->getRepository($entityName)
                ->createQueryBuilder('t')
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    
    protected function getLastRecord($entityName = null)
    {
        $entityName = $entityName ?: static::$entityName;
        
        return
            $this->entityManager->getRepository($entityName)
                ->createQueryBuilder('t')
                    ->orderBy('t.id', 'DESC')
                    ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    
    protected function runHttpRequest($url, $method = 'GET', array $server = [])
    {
        $this->httpClient->setServerParameters(array_merge([
            "HTTP_HOST" => static::$webDomain
        ], $server));

        return $this->httpClient->request($method, $url);
    }


    protected function runHttpRequestJsonReponse($url, $method = 'GET', array $server = [])
    {
        $this->httpClient->setServerParameters(array_merge([
            "HTTP_HOST" => static::$webDomain
        ], $server));

        $this->httpClient->request($method, $url);
        $response = $this->httpClient->getResponse();

        return json_decode($response->getContent(), true);
    }
}
