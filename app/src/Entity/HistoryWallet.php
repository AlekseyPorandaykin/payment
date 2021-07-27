<?php

namespace App\Entity;

use App\Repository\HistoryWalletRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Сущность нужна только для отображения структуры таблицы. Запрещено использовать сущность в проекте
 *
 * @ORM\Entity(repositoryClass=HistoryWalletRepository::class)
 * @ORM\Table(name="history_wallets",
 *     options={"comment":"История операция по кошельку клиента"},
 *     indexes={
 *      @ORM\Index(name="guid_client_idx", columns={"guid_client"}),
 *      @ORM\Index(name="created_at_idx", columns={"created_at"})
 *      }
 *     )
 */
class HistoryWallet
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", options={"comment":"Дата и время создания записи"})
     */
    private $guid;

    /**
     * @ORM\Column(type="string", name="guid_client", length=36, options={"comment":"Имя пользователя", "fixed":true})
     */
    private $clientGuid;

    /**
     * @ORM\Column(type="string", name="name_client", length=255, options={"comment":"Имя пользователя"})
     */
    private $nameClient;

    /**
     * @ORM\Column(name="amount", type="decimal", precision=15, scale=4, options={"comment":"Списанная/добавленная сумма в основной валюте"})
     */
    private $amount;

    /**
     * @ORM\Column(type="string", name="currency_code", length=3, options={"comment":"Код валюты", "fixed"=true, "default" : "USD"})
     */
    private $currencyCode;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=4, name="currency_amount", options={"comment":"Списанная/добавленная сумма в валюте кошелька"})
     */
    private $currencyAmount;

    /**
     * @ORM\Column(type="string", name="created_at", options={"comment":"Дата и время создания записи", "default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", name="comment", nullable=true, length=255, options={"comment":"Комментарий к операции"})
     */
    private $comment;
}
