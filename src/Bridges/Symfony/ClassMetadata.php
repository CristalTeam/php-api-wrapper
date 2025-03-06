<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;


use Cristal\ApiWrapper\Bridges\Symfony\Mapping\Entity;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Inflector\Inflector;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassMetadata as SerializerClassMetadata;

class ClassMetadata extends SerializerClassMetadata
{
    public const DEFAULT_REPOSITORY_CLASS = Repository::class;

    /**
     * @var Entity
     */
    private $entityAnnotation;

    public function __construct(
        string $class,
        ?ClassDiscriminatorMapping $classDiscriminatorMapping = null,
        private readonly Reader $reader = new AnnotationReader()
    ) {
        parent::__construct($class, $classDiscriminatorMapping);
    }

    public function getRepositoryClass(): string
    {
        return $this->getAnnotation()->repositoryClass ?? self::DEFAULT_REPOSITORY_CLASS;
    }

    public function getAllowedFilters(): array
    {
        return $this->getAnnotation()->allowedFilter;
    }

    public function getEntity(): ?string
    {
        return $this->getAnnotation()->entity;
    }

    public function getEntities(): ?string
    {
        if ($this->getAnnotation()->entities) {
            return $this->getAnnotation()->entities;
        }

        return Inflector::pluralize($this->getAnnotation()->entity);
    }

    public function getConnectionName(): string
    {
        return $this->getAnnotation()->connectionName;
    }

    public function getFormat(): ?string
    {
        return $this->getAnnotation()->format;
    }

    private function getAnnotation(): Entity
    {
        if ($this->entityAnnotation) {
            return $this->entityAnnotation;
        }

        return $this->entityAnnotation = $this->reader->getClassAnnotation($this->getReflectionClass(),
                Entity::class) ?? new Entity;
    }
}
