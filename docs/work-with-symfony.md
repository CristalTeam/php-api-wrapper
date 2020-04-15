# :book: Work with Symfony

Careful, this implementation is currently read-only. Help us to implement the missing parts !

## Requirements

For each integration, you need some basics :

- First, the right transport that provides HTTP requests and manages your API authentication. ([Learn more about Transports](more-about-transports.md))
- Then, a wrapper class. This is a very simple class that implements basic methods to provide an abstraction of your API. ([Learn more about Wrapper](more-about-wrapper.md))

## Register the bundle

Into your bundle file, add the Api Wrapper bundle :

```php
<?php

return [
    // ...
    Cristal\ApiWrapper\Bridges\Symfony\ApiWrapperBundle::class => ['all' => true],
];
```

## Register a connection

Each class that implements the [ConnectionInterface](../src/Bridges/Symfony/ConnectionInterface.php) is automatically registered. 
If not, you must manually tag your connection class with the `api_wrapper.connection` tag.

Follow this example for a basic connection :

```php
<?php

namespace App\Security\ApiWrapperConnection;

use Cristal\ApiWrapper\Api;
use Cristal\ApiWrapper\Bridges\Symfony\ConnectionInterface;
use Cristal\ApiWrapper\Transports\Basic;
use Curl\Curl;

class MyCustomConnection implements ConnectionInterface
{
    private $api;

    public function getName(): string
    {
        return 'my_custom_connection';
    }

    public function getApi(): Api
    {
        if ($this->api) {
            return $this->api;
        }

        $transport = new Basic('username', 'password', 'http://api.example.com/v1/', new Curl);
        return $this->api = new CustomWraper($transport);
    }
}
```

## Annotate your Entity

Basically annotate your entity with the [Entity annotation](../src/Bridges/Symfony/Mapping/Entity.php), such as below :

```php
<?php

namespace App\Entity;

use Cristal\ApiWrapper\Bridges\Symfony\Mapping as ApiWrapper;

/**
 * @ApiWrapper\Entity(
 *     entity="document",
 *     connectionName="my_custom_connection",
 *     repositoryClass="App\Repository\DocumentRepository",
 *     allowedFilter={"filter1", "filter2", "filter3"}
 * )
 */
class Document
{
    // ...
}
```

## Ready to use

Now, you can inject the [ManagerRegistry](../src/Bridges/Symfony/ManagerRegistry.php) (for example, into a Controller) :

```php
<?php

namespace App;

use App\Entity\Document;
use Cristal\ApiWrapper\Bridges\Symfony\ManagerRegistry;

class CustomClass
{
    /**
     * @var ManagerRegistry
     */
    private $registry;
    
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    public function indexAction()
    {
        $items = $this->registry->getRepository(Document::class)->findAll();    
        // ...
    } 

}
```

### Note for API Platform users

If you use Api Platform, a DataProvider has been automatically registered by the bundle,
you just need to add the ApiResource in addition to the Entity annotation.

Let's review that with our previous example :

```php
<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

use Cristal\ApiWrapper\Bridges\Symfony\Mapping as ApiWrapper;

/**
 * @ApiResource(
 *     itemOperations={"get"={}},
 *     collectionOperations={"get"={}}
 *  )
 * @ApiWrapper\Entity(
 *     entity="document",
 *     connectionName="my_custom_connection",
 *     repositoryClass="App\Repository\DocumentRepository",
 *     allowedFilter={"filter1", "filter2", "filter3"}
 * )
 */
class Document
{
    /**
     * @ApiProperty(identifier=true)
     */
    public $id;

    // ...
}
```
