# Work with Laravel

*First, be sure you followed the [part 1 and 2 here](../README.md).*

## 3. Register a connection in your provider

Into your ServiceProvider you need to create a link between instance of Api Wrapper and your model :

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cristal\ApiWrapper\Api;
use Cristal\ApiWrapper\Model;
use Cristal\ApiWrapper\Transports\Basic;
use Curl\Curl;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::setApi($this->app->make(Api::class));
    }
    
    public function register()
    {
        $this->app->bind(Api::class, function(){
            $transport = new Basic(
                'username', 
                'password', 
                'http://api.example.com/v1/', 
                $this->app->make(Curl::class)
            );
            return new Api($transport);
        });
    }

}


```

## 4. Create a model

Next, create a model that represent your object :

```php
<?php

use Cristal\ApiWrapper\Bridges\Laravel\Model;

class User extends Model
{
    // This property is directly used and pluralized by the API Wrapper (ex : getUsers)
    // on the API Wrapper.
    protected $entity = 'user';
}
```

Instead of the standard Model this bridged one returns Collections, is Serializable, Jsonable, can be used in your Routes and allows relations between an Api Model and an Eloquent Model.


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
