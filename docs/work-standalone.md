# Work standalone

If, your didn't work with Laravel, Symfony or Api Platform you can use the standalone ApiWrapper's Builder and Model inspired by Laravel Eloquent.

*First, be sure you followed the [part 1 and 2 here](../README.md).*

## 3. Configure your instance

You must create a link between Api Wrapper instance and your model :

Follow this example :

```php
<?php

use Cristal\ApiWrapper\Model;
use Cristal\ApiWrapper\Transports\Basic;
use Curl\Curl;

$transport = new Basic('username', 'password', 'http://api.example.com/v1/', new Curl);
$api = new Api($transport);
Model::setApi($api);

```
## 4. Create a model

Next, create a model that represent your object :

```php
<?php

use Cristal\ApiWrapper\Model;

class User extends Model
{
    // This property is directly used and pluralized by the API Wrapper (ex : getUsers)
    // on the API Wrapper.
    protected $entity = 'user';
}
```

## 5. Ready to use

Congratulation, you are quite ready to use your implementation like Eloquent :

```php
<?php

$activedUsers = User::where(['active' => true])->get();

foreach($activedUsers as $user){
    $user->active = false;
    $user->save();
}
```
