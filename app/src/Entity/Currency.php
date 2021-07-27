<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Dto\CurrencyDto;

/**
 * @ORM\Entity
 * @ORM\Table(name="currencies", options={"comment":"Валюты системы"})
 */
class Currency
{
    public const MAIN_CURRENCY_CODE = 'USD';
    private const LENGTH_STR_CODE = 3;
    private const MIN_NAME_STR_LENGTH = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=3, options={"comment":"Буквенный код валюты", "fixed"=true})
     */
    private string $code;

    /**
     * @ORM\Column(type="string", length=36, options={"comment":"Название валюты", "fixed"=true})
     */
    private string $name;

    /**
     * @ORM\Column(type="datetime", name="created_at", options={"comment":"Дата и время создания валюты"})
     */
    private \DateTime $createdAt;

    public function __construct(CurrencyDto $currencyDto)
    {
        if (mb_strlen($currencyDto->getCode()) !== self::LENGTH_STR_CODE) {
            throw new \DomainException(sprintf('Коде валюты может содержать только %s символа',
                self::LENGTH_STR_CODE));
        }

        if (!ctype_upper($currencyDto->getCode())) {
            throw new \DomainException('Код валюты должен состоять только из прописных символов');
        }

        if (mb_strlen($currencyDto->getName()) < self::MIN_NAME_STR_LENGTH) {
            throw new \DomainException(sprintf(
                'Название валюты не должно быть меньше %s символов',
                self::MIN_NAME_STR_LENGTH
            ));
        }
        $this->code = $currencyDto->getCode();
        $this->name = $currencyDto->getName();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return self::MAIN_CURRENCY_CODE === $this->code;
    }
}