<?php

namespace Cpro\ApiWrapper\Bridges\Laravel;

use Cpro\ApiWrapper\Model as CoreModel;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Model extends CoreModel implements Arrayable, Jsonable, UrlRoutable
{
    use ModelTrait;
}
