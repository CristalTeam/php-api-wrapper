<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

use Cristal\ApiWrapper\Bridges\Symfony\Exception\ConnectionNotFoundException;
use Cristal\ApiWrapper\Bridges\Symfony\Exception\RepositoryNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ManagerRegistry
{
    /**
     * @var iterable|ConnectionInterface[]
     */
    private $connections;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRepository(string $entityName): Repository
    {
        $metadata = $this->getMetadataFromClass($entityName);

        if(null === $metadata){
            throw new RepositoryNotFoundException(sprintf('Unable to find API repository for %s.', $entityName));
        }

        return $this->container->get($metadata->getRepositoryClass())->setupRepository($this, $entityName);
    }

    public function getMetadataFromClass(string $entityName): ?ClassMetadata
    {
        $metadata = new ClassMetadata($entityName);
        if(null === $metadata->getEntity()){
            return null;
        }
        return $metadata;
    }

    /**
     * @throws ConnectionNotFoundException
     */
    public function getConnection(string $connectionName)
    {
        if(!isset($this->connections[$connectionName])){
            throw new ConnectionNotFoundException(
                sprintf('Unable to find connection named %s.', $connectionName)
            );
        }

        return $this->connections[$connectionName];
    }

    public function addConnection(ConnectionInterface $connection): ManagerRegistry
    {
        $this->connections[$connection->getName()] = $connection;

        return $this;
    }
}
