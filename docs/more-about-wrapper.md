# The Wrapper

A wrapper is a standalone class that must follow a specific implementation.
For example, consider a User (create, read, delete and update), implementation. Here's what your wrapper must look like :

```php
<?php // This is a homemade wrapper

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
