# :book: Work with Laravel

## Requirements

For each integration, you need some basics :

- First, the right transport that provides HTTP requests and manages your API authentication. ([Learn more about Transports](more-about-transports.md))
- Then, a wrapper class. This is a very simple class that implements basic methods to provide an abstraction of your API. ([Learn more about Wrapper](more-about-wrapper.md))

## Register a connection in your provider

You must create a link between Api Wrapper instance and your model. We recommend that you don't use directly the basic model, it's more convenient if you have several APIs to manage :

```php
<?php

namespace App;

use Cristal\ApiWrapper\Bridges\Laravel\Model;

class MyModel extends Model
{
    // Nothing here
}

```

> **Tips !** Instead of the standard Model, this bridged one returns Collections, is Serializable, Jsonable, can be used in your Routes and allows relations between an Api Model and an Eloquent Model.

Next, into your ServiceProvider, you need to create a link between instance of your custom Api Wrapper and your custom model :

```php
<?php

namespace App\Providers;

use App\MyModel;
use App\CustomWrapper;
use Illuminate\Support\ServiceProvider;
use Cristal\ApiWrapper\Transports\Basic;
use Curl\Curl;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        MyModel::setApi($this->app->make(CustomWrapper::class));
    }
    
    public function register()
    {
        $this->app->bind(CustomWrapper::class, function(){
            $transport = new Basic(
                'username', 
                'password', 
                'http://api.example.com/v1/', 
                $this->app->make(Curl::class)
            );
            return new CustomWrapper($transport);
        });
    }

}

```

As you can see, if you need to provide another API Wrapper, you can create a new empty model to set another API Wrapper.

## Create a model

Next, create a model that represents your object :

```php
<?php

namespace App;

class User extends MyModel
{
    // This property is directly used and pluralized by the API Wrapper (ex : getUsers).
    protected $entity = 'user';

    // If your API resource can be identified with a unique key you can define 
    // the primary key. By default it is 'id'.
    protected $primaryKey = 'id';
}
```

## Ready to use

Congratulation, you are quite ready to use your implementation like Eloquent :

```php
<?php

Route::get('/users', static function () {
    $activedUsers = User::where(['active' => true])->get();

    foreach($activedUsers as $user){
        $user->active = false;
        $user->save();
    }
});

Route::get('/users/{user}', static function (User $user) {
    return view('profile', ['user' => $user]);
});

```

# Learn more

- [Lean more about models and relations](more-about-models.md)
- [Lean more about the builder](more-about-builder.md)
