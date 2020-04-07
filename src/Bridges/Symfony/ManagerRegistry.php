<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

use Cristal\ApiWrapper\Bridges\Symfony\Exception\ConnectionNotFoundException;
use Cristal\ApiWrapper\Bridges\Symfony\Exception\RepositoryNotFoundException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ManagerRegistry
{
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * @var iterable|ConnectionInterface[]
     */
    private iterable $connections;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    public function getRepository(string $entityName): Repository
    {
        $metadata = $this->getMetadataFromClass($entityName);

        if(null === $metadata){
            throw new RepositoryNotFoundException(sprintf('Unable to find API repository for %s.', $entityName));
        }

        $repositoryName = $metadata->getRepositoryClass();
        return new $repositoryName(
            new ClassMetadata($entityName),
            $this->denormalizer,
            $this->getConnection($metadata->getConnectionName())
        );
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
