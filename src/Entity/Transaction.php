<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private ?int $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HotelGroup", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: HotelGroup::class, inversedBy: "transactions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?HotelGroup $hotelGroup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resort;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $confirmationNo;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private ?int $reservationId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nameId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $roomNo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $arrivalDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $departureDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $trxCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    /**
     * @ORM\Column(type="float")
     */
    #[ORM\Column(type: 'float')]
    private ?float $grossAmount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $tcGroup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $transactionCategory;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(int $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getHotelGroup(): ?HotelGroup
    {
        return $this->hotelGroup;
    }

    public function setHotelGroup(?HotelGroup $hotelGroup): self
    {
        $this->hotelGroup = $hotelGroup;

        return $this;
    }

    public function getResort(): ?string
    {
        return $this->resort;
    }

    public function setResort(?string $resort): self
    {
        $this->resort = $resort;

        return $this;
    }

    public function getConfirmationNo(): ?string
    {
        return $this->confirmationNo;
    }

    public function setConfirmationNo(?string $confirmationNo): self
    {
        $this->confirmationNo = $confirmationNo;

        return $this;
    }

    public function getReservationId(): ?int
    {
        return $this->reservationId;
    }

    public function setReservationId(int $reservationId): self
    {
        $this->reservationId = $reservationId;

        return $this;
    }

    public function getNameId(): ?int
    {
        return $this->nameId;
    }

    public function setNameId(?int $nameId): self
    {
        $this->nameId = $nameId;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRoomNo(): ?string
    {
        return $this->roomNo;
    }

    public function setRoomNo(?string $roomNo): self
    {
        $this->roomNo = $roomNo;

        return $this;
    }

    public function getArrivalDate(): ?string
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(?string $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getDepartureDate(): ?string
    {
        return $this->departureDate;
    }

    public function setDepartureDate(?string $departureDate): self
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getTrxCode(): ?string
    {
        return $this->trxCode;
    }

    public function setTrxCode(?string $trxCode): self
    {
        $this->trxCode = $trxCode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGrossAmount(): ?float
    {
        return $this->grossAmount;
    }

    public function setGrossAmount(float $grossAmount): self
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    public function getTcGroup(): ?string
    {
        return $this->tcGroup;
    }

    public function setTcGroup(?string $tcGroup): self
    {
        $this->tcGroup = $tcGroup;

        return $this;
    }

    public function getTransactionCategory(): ?string
    {
        return $this->transactionCategory;
    }

    public function setTransactionCategory(?string $transactionCategory): self
    {
        $this->transactionCategory = $transactionCategory;

        return $this;
    }
}
