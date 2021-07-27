<?php

declare(strict_types=1);

namespace App\Repository;

use App\Dto\ClientDto;
use App\Entity\Client;
use App\Exception\NotFoundEntityException;

class ClientRepository extends AbstractRepository
{
    /**
     * @param ClientDto $clientDto
     * @return Client|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByParam(ClientDto $clientDto): ?Client
    {
        return $this->entityManager->createQueryBuilder()
            ->select('client')
            ->from(Client::class, 'client')
            ->andWhere('client.name = :name')
            ->andWhere('client.country = :country')
            ->andWhere('client.city = :city')
            ->setParameters([
                'name'    => $clientDto->getName(),
                'country' => $clientDto->getCountry(),
                'city'    => $clientDto->getCity(),
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $clientGuid
     * @return Client
     * @throws NotFoundEntityException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByGuid(string $clientGuid): Client
    {
        $client = $this->entityManager->createQueryBuilder()
            ->select('client')
            ->from(Client::class, 'client')
            ->where('client.guid = :client')
            ->setParameter('client', $clientGuid)
            ->getQuery()
            ->getOneOrNullResult();
        if ($client === null) {
            throw new NotFoundEntityException("Не найден клиент с guid='{$clientGuid}'");
        }

        return $client;
    }
}