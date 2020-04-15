# :book: Work standalone

If you are not working with Laravel, Symfony or Api Platform, you can use the standalone Builder and Models of Api Wrapper inspired by Laravel Eloquent.

## Requirements

For each integration, you need some basics :

- First, the right transport that provides HTTP requests and manages your API authentication. ([Learn more about Transports](more-about-transports.md))
- Then, a wrapper class. This is a very simple class that implements basic methods to provide an abstration of your API. ([Learn more about Wrapper](more-about-wrapper.md))


## Configure your instance

You must create a link between Api Wrapper instance and your model. We recommend that you don't use directly the basic model, it's more convenient if you have several APIs to manage :

```php
<?php

namespace App;

use Cristal\ApiWrapper\Model;

class MyModel extends Model
{
    // Nothing here
}

```

Next, follow this example to register your API Wrapper into your new model :

```php
<?php

use App\MyModel;
use Curl\Curl;
use Cristal\ApiWrapper\Transports\Basic;

$transport = new Basic('username', 'password', 'http://api.example.com/v1/', new Curl);
$api = new YourCustomAPIWrapper($transport);
MyModel::setApi($api);

```

As you can see, if we need to provide another API Wrapper, we can create a new empty model to set another API Wrapper.

## Create a model

Next, create a model that represent your object :

```php
<?php

namespace App;

class User extends MyModel
{
    // This property is directly used and pluralized by the API Wrapper (ex : getUsers)
    protected $entity = 'user';
}
```

## Ready to use

Congratulation, you are quite ready to use your implementation like Eloquent :

```php
<?php

$activedUsers = User::where(['active' => true])->get();

foreach($activedUsers as $user){
    $user->active = false;
    $user->save();
}
```

# Learn more

- [Lean more about models and relations](more-about-models.md)
- [Lean more about the builder](more-about-builder.md)
