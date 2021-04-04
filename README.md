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
