<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Dto\ClientDto;

/**
 * @ORM\Entity
 * @ORM\Table(name="clients",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="uniq_name_country_city", columns={"name", "country", "city"})},
 *     options={"comment":"Клиенты системы"}
 *     )
 */
class Client
{
    private const MIN_NAME_STR_LENGTH = 5;
    private const MIN_COUNTRY_STR_LENGTH = 5;
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=36, options={"comment":"Идентификатор клиента", "fixed":true})
     */
    private string $guid;

    /**
     * @ORM\Column(type="string", options={"comment":"Имя пользователя"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", options={"comment":"Страна регистрации"})
     */
    private string $country;

    /**
     * @ORM\Column(type="string", options={"comment":"Город регистрации"})
     */
    private string $city;

    /**
     * @ORM\Column(type="datetime", name="created_at", options={"comment":"Дата и время создания клиента"})
     */
    private \DateTime $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Wallet", mappedBy="client")
     */
    private Wallet $wallet;

    public function __construct(string $guid, ClientDto $clientDto)
    {
        if (mb_strlen($clientDto->getName()) < self::MIN_NAME_STR_LENGTH) {
            throw new \DomainException(sprintf('Имя пользователя не должно быть меньше %s символов',
                self::MIN_NAME_STR_LENGTH));
        }
        if (mb_strlen($clientDto->getCountry()) < self::MIN_COUNTRY_STR_LENGTH) {
            throw new \DomainException(sprintf('Название страны не должно быть меньше %s символов',
                self::MIN_COUNTRY_STR_LENGTH));
        }
        $this->guid = $guid;
        $this->name = $clientDto->getName();
        $this->country = $clientDto->getCountry();
        $this->city = $clientDto->getCity();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return Wallet
     */
    public function getWallet(): Wallet
    {
        return $this->wallet;
    }
}