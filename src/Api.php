<?php

namespace Starif\ApiWrapper;

class Api
{
    /**
     * @var TransportInterface
     */
    private $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function getTransport()
    {
        return $this->transport;
    }

    public function getClients(array $filters = []): array
    {
        return $this->transport->request('/clients', $filters);
    }

    public function getClient(int $id): array
    {
        return $this->transport->request('/client/'.$id);
    }

    public function updateClient(int $id, $attributes): array
    {
        return $this->transport->request('/client/'.$id, $attributes, 'put');
    }

    public function createClient($attributes): array
    {
        return $this->transport->request('/client', $attributes, 'post');
    }

    public function deleteClient(int $id): array
    {
        return $this->transport->request('/client/'.$id, [], 'delete');
    }

    public function getCatalogues(array $filters = []): array
    {
        return $this->transport->request('/catalogues', $filters);
    }

    public function getCatalogue(int $id): array
    {
        return $this->transport->request('/catalogue/'.$id);
    }

    public function updateCatalogue(int $id, $attributes): array
    {
        return $this->transport->request('/catalogue/'.$id, $attributes, 'put');
    }

    public function createCatalogue($attributes): array
    {
        return $this->transport->request('/catalogue', $attributes, 'post');
    }

    public function deleteCatalogue(int $id): array
    {
        return $this->transport->request('/catalogue/'.$id, [], 'delete');
    }

    public function getTarifs(array $filters = []): array
    {
        return $this->transport->request('/tarifs', $filters);
    }

    public function getTarif(int $id): array
    {
        return $this->transport->request('/tarif/'.$id);
    }

    public function updateTarif(int $id, $attributes): array
    {
        return $this->transport->request('/tarif/'.$id, $attributes, 'put');
    }

    public function createTarif($attributes): array
    {
        return $this->transport->request('/tarif', $attributes, 'post');
    }

    public function deleteTarif(int $id): array
    {
        return $this->transport->request('/tarif/'.$id, [], 'delete');
    }

    public function getMateriels(array $filters = []): array
    {
        return $this->transport->request('/materiels', $filters);
    }

    public function getMateriel(int $id): array
    {
        return $this->transport->request('/materiel/'.$id);
    }

    public function updateMateriel(int $id, $attributes): array
    {
        return $this->transport->request('/materiel/'.$id, $attributes, 'put');
    }

    public function createMateriel($attributes): array
    {
        return $this->transport->request('/materiel', $attributes, 'post');
    }

    public function deleteMateriel(int $id): array
    {
        return $this->transport->request('/materiel/'.$id, [],'delete');
    }
}