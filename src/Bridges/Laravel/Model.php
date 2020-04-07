<?php

namespace Cristal\ApiWrapper\Bridges\Laravel;

use Cristal\ApiWrapper\Model as CoreModel;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Model extends CoreModel implements Arrayable, Jsonable, UrlRoutable
{
    use HasEloquentRelations;
}
