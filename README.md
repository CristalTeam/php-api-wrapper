# PHP API Wrapper

[![Latest Stable Version](https://img.shields.io/packagist/v/cristal/php-api-wrapper.svg?style=flat-square)](https://packagist.org/packages/cristal/php-api-wrapper)
[![GitHub issues](https://img.shields.io/github/issues/cristalTeam/php-api-wrapper.svg?style=flat-square)](https://github.com/cristalTeam/php-api-wrapper/issues)
[![GitHub license](https://img.shields.io/github/license/cristalTeam/php-api-wrapper.svg?style=flat-square)](https://github.com/cristalTeam/php-api-wrapper/blob/master/LICENSE)

PHP API Wrapper is a smart stack based on a couple of a Transport and a smart Wrapper for your API. 
It is designed to be easily integrated into your projects thanks to bridges for **Laravel, Symfony, API Platform** and a **standalone stack**.

## :rocket: Installation using Composer

```sh
composer require cristal/php-api-wrapper
```

## :eyes: Quick view 

```php
<?php

// Configure your API

use Cristal\ApiWrapper\Model;
use Cristal\ApiWrapper\Transports\Basic;
use App\User;
use Curl\Curl;

$transport = new Basic('username', 'password', 'http://api.example.com/v1/', new Curl);
$api = new Api($transport);

Model::setApi($api);

// Use your model like Eloquent (Usage with Symfony is significantly different)

$activedUsers = User::where(['active' => true])->get();

foreach($activedUsers as $user){
    $user->active = false;
    $user->save();
}
```

## :book: Chose your stack

### :point_right: Start wihout Laravel or Symfony

If you decide to work without Laravel or Symfony, PHP Api Wrapper comes with a standalone Builder and a Model largely inspired by Eloquent, but really standalone. I promise !

[Start without Laravel or Symfony](docs/work-standalone.md)

### :point_right: Start with Laravel

This is actualy the powerfull usage of API Wrapper. If you decide to use PHP API Wrapper with Laravel the integration approaches perfection. The builder returns Collections, all models are usable with the **Laravel Route Binding** (this is really impressive). And icing on the cake, **you can create complexes relations between Eloquent and PHP API Wrapper**.

[Start with Laravel](docs/work-with-laravel.md)


### :point_right: Start with Symfony (and optionally Api Platform)

This implementation is realy interesting too, the Symfony bridge provide you a Repository implementing the Doctrine RepositoryInterface which hydrates your entities. A Manager is also available which allows you to manage repositories and its connections. If you are using API Platform this is fully compatible. A API Platform Data Provider is also registered.

:warning: *Careful, this implementation is currently read-only. Help us to implement the missing parts !*

[Start with Symfony](docs/work-with-symfony.md)
