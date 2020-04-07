# PHP API Wrapper

PHP API Wrapper is a Laravel Eloquent like, built to work with APIs. The integration of each API consists of three steps:

- The Transport
- The Wrapper
- The Models and Builder

## 1. The Transport

The Transport is an implementation of `TransportInterface`, it manages the API Autentication, 
returns desialized data, and manage HTTP errors with `ApiException` and childs (see `src/Exceptions`).

Some implementations already exists into the namespace `Cristal\ApiWrapper\Transports` :

- `Transport` Generic JSON API without authentication
- `Bearer` Extends of `Transport` supports bearers tokens
- `Basic` Extends of `Transport` supports basic auth
- `OAuth2` Extends of `Transport` supports OAuth2 ([see doc](docs/transports/oauth2.md))

Example of usage :

```php
<?php

use Cristal\ApiWrapper\Transports\Basic;
use Curl\Curl;

$transport = new Basic('username', 'password', 'http://api.example.com/v1/', new Curl);
$users = $transport->request('/users');

```

## 2. The Wrapper

A wrapper is a standalone class that must follow a specific implementation.
For example, consider a User (create, read, delete and update), implementation. Here's what your wrapper should look like :

```php
<?php // This is a home made wrapper

class CustomWraper
{
    private $transport;
    
    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }
    //...
    public function getUser($id) // Retrive just ONE user
    {
        return $this->transport->request('/user/'.$id);
    }

    public function getUsers(array $filters) // Retrive multiple users
    {
        return $this->transport->request('/users', $filters);
    }

    public function createUser(array $data) 
    {
        return $this->transport->request('/users', $data, 'post');
    }

    public function updateUser($id, array $data)
    {
        return $this->transport->request('/user/'.$id, $data, 'put');
    }

    public function deleteUser($id)
    {
        return $this->transport->request('/user/'.$id, 'delete');
    }
    //...
}
```

This way can be very redundant, which is why you can extend from `Cristal\ApiWrapper\Api`.
This implementation forward methods with magics `__call` (for exemple with User) :

- `getUser(...)` to the method `findOne('user', ...)`
- `getUsers(...)` to the method `findAll('users', ...)`
- `createUser(...)` to the method `create('user', ...)`
- `updateUser(...)` to the method `update('user', ...)`
- `deleteUser(...)` to the method `delete('user', ...)`

This new implementation will probably look like that :

```php
<?php

class CustomWraper extends Api
{
    // Nothing here...
}
```

# 3. Work with Models

First, you must create a link between Api Wrapper instance and your model :

```php
<?php

use Cristal\ApiWrapper\Model;
use Curl\Curl;

$transport = new Basic('username', 'password', 'http://api.example.com/v1/', new Curl);
$api = new Api($transport);
Model::setApi($api);

```
If your are working on Laravel, implement it on your ServiceProvider.

Next, create a model that represente your entity :

```php
<?php

use Cristal\ApiWrapper\Model;

class User extends Model
{
    // This property is directly use and pluralize (ex : getUsers)
    // on the API Wrapper.
    protected $entity = 'user';
}
```

Congratulation, you are quite ready to use your implementation like Eloquent :

```php
<?php

$activedUser = User::where(['active' => true]);

foreach($activedUser as $user){
    $user->active = false;
    $user->save();
}
```
