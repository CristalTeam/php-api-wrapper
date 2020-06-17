<?php

namespace Cristal\ApiWrapper\Bridges\Symfony\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Entity
{
    /**
     * @var string
     */
    public $entity;

    /**
     * @var string
     */
    public $entities;

    /**
     * @var string
     */
    public $repositoryClass;

    /**
     * @var string
     */
    public $connectionName;

    /**
     * @var string|null
     */
    public $format;

    /**
     * @var array
     */
    public $allowedFilter = [];
}
