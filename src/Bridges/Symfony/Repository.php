<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

use Cristal\ApiWrapper\Api;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Repository implements ObjectRepository
{
    /**
     * Those criteria are forwarded to the API Wrapper.
     */
    public const CRITERIA_ORDER_BY = 'sort';
    public const CRITERIA_LIMIT = 'limit';
    public const CRITERIA_PAGE = 'page';

    /**
     * When API returns a paginate content, that defines how to map these values.
     */
    public const PAGINATION_MAPPING_CHILD = null;
    public const PAGINATION_MAPPING_TOTAL = 'total';
    public const PAGINATION_MAPPING_PER_PAGE = 'per_page';
    public const PAGINATION_MAPPING_CURRENT_PAGE = 'current_page';

    /**
     * To provide a correct pagination, limit must be defined by default.
     */
    public const DEFAULT_LIMIT = 50;

    /**
     * @var ClassMetadata
     */
    private $class;

    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var ManagerRegistry
     */
    protected $manager;

    public function __construct(ManagerRegistry $manager, DenormalizerInterface $denormalizer, $entity = null)
    {
        $this->manager = $manager;
        $this->denormalizer = $denormalizer;
        $this->setupRepository($entity);
    }

    protected function getApi(): Api
    {
        return $this->connection->getApi();
    }

    public function getMetadata(): ClassMetadata
    {
        return $this->class;
    }

    /**
     * @inheritDoc
     */
    public function find($id)
    {
        return $this->instanciateEntity(
            $this->getApi()->{'get' . ucfirst($this->class->getEntity())}($id)
        );
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->findBy([]);
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $page = null)
    {
        if ($orderBy) {
            $criteria[static::CRITERIA_ORDER_BY] = $orderBy;
        }

        $criteria[static::CRITERIA_LIMIT] = $limit ?? static::DEFAULT_LIMIT;
        $criteria[static::CRITERIA_PAGE] = $page ?? 1;

        $results = $this->getApi()->{'get' . ucfirst($this->class->getEntities())}(
            $this->filterCriteria($criteria)
        );

        return $this->paginate($results, $criteria);
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $criteria)
    {
        return $this->findBy($criteria)[0];
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return $this->class->getName();
    }

    protected function getMembers($results)
    {
        if (static::PAGINATION_MAPPING_CHILD) {
            return $results[static::PAGINATION_MAPPING_CHILD] ?? [];
        }

        return
            $results['hydra:member'] ??
            $results[$this->class->getEntities()] ??
            $results[$this->class->getEntity()] ??
            $results['data'] ??
            $results['items'] ??
            $results['results'] ??
            $results['result'] ??
            $results;
    }

    protected function filterCriteria(array $criteria)
    {
        $allowedFilters = array_merge(
            $this->class->getAllowedFilters(),
            [static::CRITERIA_ORDER_BY, static::CRITERIA_LIMIT, static::CRITERIA_PAGE]
        );

        return array_intersect_key($criteria, array_flip($allowedFilters));
    }

    protected function paginate($results, $criteria): iterable
    {
        return new Paginator(
            $this->instanciateEntity($this->getMembers($results), true),
            $results[static::PAGINATION_MAPPING_TOTAL] ?? count($results),
            $results[static::PAGINATION_MAPPING_PER_PAGE] ?? $criteria[static::CRITERIA_LIMIT] ?? null,
            $results[static::PAGINATION_MAPPING_CURRENT_PAGE] ?? $criteria[static::CRITERIA_PAGE] ?? null
        );
    }

    protected function instanciateEntity($data, $multiple = false)
    {
        if (!is_array($data)) {
            return null;
        }

        $denormalize = function ($data) {
            return $this->denormalizer->denormalize($data, $this->getClassName());
        };

        if ($multiple) {
            return array_map(static function ($entity) use ($denormalize) {
                return $denormalize($entity);
            }, $data);
        }

        return $denormalize($data);
    }

    /**
     * @param string|null $entity
     */
    public function setupRepository($entity): self
    {
        if ($entity) {
            $this->class = new ClassMetadata($entity);
            $this->connection = $this->manager->getConnection($this->class->getConnectionName());
        }

        return $this;
    }
}
