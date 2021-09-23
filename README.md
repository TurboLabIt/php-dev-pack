# TurboLab.it Base Bundle

A collection of reusable Symfony components by TurboLab.it.

## 🧪 Test it

````bash
git clone git@github.com:TurboLabIt/TLIBaseBundle.git
cd TLIBaseBundle
composer install
phpunit
````


## 📦 Install it with composer

````bash
symfony composer config repositories.0 '{"type": "vcs", "url": "https://github.com/TurboLabIt/TLIBaseBundle.git", "no-api": true}'
symfony composer require turbolabit/tli-base-bundle:dev-master
````


## 🏗️ Service Entity

A *Service Entity* (SE) is a building block for a specific area of the application. Each SE is design to wrap (as in: *has-an*) *entity*, but it can store additional data (from API response, for example). Each SE is also an *Active Record* for the related entity (welcome back, `->save()`) but, most important of all, **contains the business logic** for the related area of the application.

For maximum portability, create an abstract class on the project (`src/Service/AbstractBase/BaseServiceEntity.php`):

````php
<?php
namespace App\Service\AbstractBase;

use TurboLabIt\TLIBaseBundle\Service\ServiceEntity\ServiceEntity;


abstract class BaseServiceEntity extends ServiceEntity
{
}

````

Now create your own SE (`src/Service/Article/Article.php`):

````php
<?php
namespace App\Service\Article;

use App\Exception\ArticleNotFoundException;
use App\Service\AbstractBase\BaseServiceEntity;
use Doctrine\ORM\EntityManagerInterface;


class Article extends BaseServiceEntity
{
    public function __construct(
        EntityManagerInterface $em, ArticleNotFoundException $articleNotFoundException
        // ...
    ){
        parent::__construct($em, \App\Entity\Article::class, $articleNotFoundException);
        // ...
    }
}

````

Some cool methods of SEs:

- `->loadById(7)`
- `->loadByFieldsValues(["title" => "My title", "type" => "news"])`
- `->setData($arrApiResponseData)`
- `->checkNotNullInput($myVar)`
- [more](https://github.com/TurboLabIt/TLIBaseBundle/edit/master/src/Service/ServiceEntity/ServiceEntity.php)


## 🏗️ Service Entity Collection

A *Service Entity Collection* (SEC) is an itrable structure wich holds multiple instances of a related SE.

For maximum portability, create an abstract class on the project (`src/Service/AbstractBase/BaseServiceEntityCollection.php`):

````php
<?php
namespace App\Service\AbstractBase;

use TurboLabIt\TLIBaseBundle\Service\ServiceEntity\ServiceEntityCollection;


abstract class BaseServiceEntityCollection extends ServiceEntityCollection
{
}

````

Now create your own SEC (`src/Service/Article/ArticleCollection.php`)

````php
<?php
namespace App\Service\Article;

use App\Exception\ArticleNotFoundException;
use App\Service\AbstractBase\BaseServiceEntityCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;


abstract class ArticleCollection extends BaseServiceCollection
{
    public function __construct(
        EntityManagerInterface $em, ArticleNotFoundException $notFoundException,
        // ...
    ) {
        parent::__construct($em, \App\Entity\Article::class, $notFoundException);
        // ...
    }


    public function createService()
    {
        return new Article(
            $this->em, $this->notFoundException
            // ...
            );
        );
    }
}

````

Some cool methods of SEs:

- `->loadByIds(7,15,24)`
- `->loadAll()`
- `->toCsv(',', 'getTitle')`
- [more](https://github.com/TurboLabIt/TLIBaseBundle/edit/master/src/Service/ServiceEntity/ServiceEntityCollection.php)


## 🔁 Trait Foreachable

Use it to quickly create collections of objects. You can then iterate over it.

````php
 use TurboLabIt\TLIBaseBundle\Traits\Foreachable;
 
 class Listing implements \Iterator, \Countable, \ArrayAccess
 {
    use Foreachable;
    ...
    ...
 }
 
 ...
 
 $collListing = new Listing();
 foreach($collListing as $oneItem) {
 
    ...
 }
````


## 🗄️ Traits //ableEntity

Shorten your entities with these traits. They provide property, getter, setter for various common fields.

You'll probably want to add [Timestampable](https://symfony.com/doc/current/bundles/StofDoctrineExtensionsBundle/installation.html) too:

```bash
symfony composer require stof/doctrine-extensions-bundle
```

In `app/config/packages/stof_doctrine_extensions.yml`:

````yaml
stof_doctrine_extensions:
   orm:
      default:
          timestampable: true
````

Spice up your entity:

````php
use TurboLabIt\TLIBaseBundle\Traits\IdableEntity;
use TurboLabIt\TLIBaseBundle\Traits\NameableEntity;
use TurboLabIt\TLIBaseBundle\Traits\TitleableEntity;
use TurboLabIt\TLIBaseBundle\Traits\PublicableEntity;
use TurboLabIt\TLIBaseBundle\Traits\ViewableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;


class DummyEntity
{
    use IdableEntity, NameableEntity, TitleableEntity, PublicableEntity, ViewableEntity, TimestampableEntity;
    ...
}
````

### 1️⃣ Unique

Need some //ableEntity to be unique?

````php
/**
 * ...
 * @ORM\Table(name="dummy",uniqueConstraints={@ORM\UniqueConstraint(name="title", columns={"title"})})
 */
class DummyEntity
{
    ...
}
````

### 🛑 Idable::setId()

The `Idable` Traits provides an autoincrement `id` field. This is the way.

If you need to manually set the `id` on a new entity (i.e.: because you're importing data), you can also use the provided `setId()` method.

But this won't work automatically. Before `setId()` can actually work, you have to manually disable the autoincrement on the EntityManager:

````php
public function __construct(EntityManagerInterface $em, EntityManagerOptions $emOptions)
{
    $this->em = $em;
    $this->emOptions = $emOptions;
}


protected function disableAutoincrement()
{
    $this->emOptions->disableAutoincrement($this->em, [
        Article::class, File::class, Image::class, Tag::class
    ]);
}
````

Then just call `disableAutoincrement()` and you're ready to `setId()` at will - yes, the `Auto Increment` metadata on the tables will rise on its own.


## ➕ Trait AtomicFieldIncrease

Need to increase a field in an atomic manner? Put this in your entity repository:

````php
use TurboLabIt\TLIBaseBundle\Traits\AtomicFieldIncrease

class DummyRepository
{
    use AtomicFieldIncrease;
    public function increaseViews(int $entityId)
    {
        return $this->atomicFieldIncrease('views', $entityId);
    }
    ...
}
````

Run it when needed:

````php
$repoArticles->increaseViews(744);
````

Done! You just increased the `views` field on the record `id = 744` of the table.
