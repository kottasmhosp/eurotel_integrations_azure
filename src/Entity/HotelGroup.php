<?php

namespace App\Entity;

use App\Repository\HotelGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HotelGroupRepository")
 */
#[ORM\Entity(repositoryClass: HotelGroupRepository::class)]
class HotelGroup
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
     * @ORM\Column(type="integer", options={"comment":"UTC timestamp"})
     */
    #[ORM\Column(type: 'integer', nullable: false, options: ["comment" =>"UTC timestamp"])]
    private ?int $created;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"comment":"UTC timestamp"})
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ["comment" =>"UTC timestamp"])]
    private ?int $modified;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $groupName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $baseUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $loginUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $inhouseUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $checkoutUrl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="hotelGroup")
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: "hotelGroup")]
    private Collection $reservations;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(type: 'boolean', nullable: false, options: ["default" => false])]
    private bool $integrationInProgress;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="hotelGroup")
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: "hotelGroup")]
    private Collection $transactions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrxCode", mappedBy="hotelGroup")
     */
    #[ORM\OneToMany(targetEntity: TrxCode::class, mappedBy: "hotelGroup")]
    private Collection $trxCodes;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(name: 'mh3as_proxy', type: 'boolean', nullable: false, options: ["default" => false])]
    private bool $mh3AsProxy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $inhouseUrlFromTo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $futureReservationsIntegrationInProgress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $futureReservationsUrl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FutureReservation", mappedBy="hotelGroup")
     */
    #[ORM\OneToMany(targetEntity: FutureReservation::class, mappedBy: "hotelGroup")]
    private Collection $futureReservations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $UtfOffset;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $checkoutIsLocked;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(name: 'mh3as_proxy_v2', type: 'boolean', nullable: false, options: ["default" => false])]
    private ?bool $Mh3AsProxyV2 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titanReservationUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CreationDateReservationsUrl = null;

    #[ORM\Column(nullable: true)]
    private ?bool $CreationDateIntegrationInProgress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tenant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $daysAhead = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->trxCodes = new ArrayCollection();
        $this->futureReservations = new ArrayCollection();
    }

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

    public function getModified(): ?int
    {
        return $this->modified;
    }

    public function setModified(?int $modified): self
    {
        $this->modified = $modified;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): self
    {
        $this->groupName = $groupName;

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function getLoginUrl(): ?string
    {
        return $this->loginUrl;
    }

    public function setLoginUrl(string $loginUrl): self
    {
        $this->loginUrl = $loginUrl;

        return $this;
    }

    public function getInhouseUrl(): ?string
    {
        return $this->inhouseUrl;
    }

    public function setInhouseUrl(string $inhouseUrl): self
    {
        $this->inhouseUrl = $inhouseUrl;

        return $this;
    }

    public function getCheckoutUrl(): ?string
    {
        return $this->checkoutUrl;
    }

    public function setCheckoutUrl(?string $checkoutUrl): self
    {
        $this->checkoutUrl = $checkoutUrl;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getReservations(): ?Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setHotelGroup($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getHotelGroup() === $this) {
                $reservation->setHotelGroup(null);
            }
        }

        return $this;
    }

    /**
     * @param Collection $reservations
     */
    public function setReservations(Collection $reservations): void
    {
        $this->reservations = $reservations;
    }

    public function getIntegrationInProgress(): ?bool
    {
        return $this->integrationInProgress;
    }

    public function setIntegrationInProgress(bool $integrationInProgress): self
    {
        $this->integrationInProgress = $integrationInProgress;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getTransactions(): ?Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setHotelGroup($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getHotelGroup() === $this) {
                $transaction->setHotelGroup(null);
            }
        }

        return $this;
    }

    /**
     * @param Collection $transactions
     */
    public function setTransactions(Collection $transactions): void
    {
        $this->transactions = $transactions;
    }

    /**
     * @return Collection|null
     */
    public function getTrxCodes(): ?Collection
    {
        return $this->trxCodes;
    }

    public function addTrxCode(TrxCode $trxCode): self
    {
        if (!$this->trxCodes->contains($trxCode)) {
            $this->trxCodes[] = $trxCode;
            $trxCode->setHotelGroup($this);
        }

        return $this;
    }

    public function removeTrxCode(TrxCode $trxCode): self
    {
        if ($this->trxCodes->contains($trxCode)) {
            $this->trxCodes->removeElement($trxCode);
            // set the owning side to null (unless already changed)
            if ($trxCode->getHotelGroup() === $this) {
                $trxCode->setHotelGroup(null);
            }
        }

        return $this;
    }

    /**
     * @param Collection $trxCodes
     */
    public function setTrxCodes(Collection $trxCodes): void
    {
        $this->trxCodes = $trxCodes;
    }

    public function getMh3AsProxy(): ?bool
    {
        return $this->mh3AsProxy;
    }

    public function setMh3AsProxy(bool $mh3AsProxy): self
    {
        $this->mh3AsProxy = $mh3AsProxy;

        return $this;
    }

    public function getInhouseUrlFromTo(): ?string
    {
        return $this->inhouseUrlFromTo;
    }

    public function setInhouseUrlFromTo(?string $inhouseUrlFromTo): self
    {
        $this->inhouseUrlFromTo = $inhouseUrlFromTo;

        return $this;
    }

    public function getFutureReservationsIntegrationInProgress(): ?bool
    {
        return $this->futureReservationsIntegrationInProgress;
    }

    public function setFutureReservationsIntegrationInProgress(?bool $futureReservationsIntegrationInProgress): self
    {
        $this->futureReservationsIntegrationInProgress = $futureReservationsIntegrationInProgress;

        return $this;
    }

    public function getFutureReservationsUrl(): ?string
    {
        return $this->futureReservationsUrl;
    }

    public function setFutureReservationsUrl(?string $futureReservationsUrl): self
    {
        $this->futureReservationsUrl = $futureReservationsUrl;

        return $this;
    }

    /**
     * @param Collection $futureReservations
     */
    public function setFutureReservations(Collection $futureReservations): void
    {
        $this->futureReservations = $futureReservations;
    }

    /**
     * @return Collection|null
     */
    public function getFutureReservations(): ?Collection
    {
        return $this->futureReservations;
    }

    public function addFutureReservation(FutureReservation $futureReservation): self
    {
        if (!$this->futureReservations->contains($futureReservation)) {
            $this->futureReservations[] = $futureReservation;
            $futureReservation->setHotelGroup($this);
        }

        return $this;
    }

    public function removeFutureReservation(FutureReservation $futureReservation): self
    {
        if ($this->futureReservations->contains($futureReservation)) {
            $this->futureReservations->removeElement($futureReservation);
            // set the owning side to null (unless already changed)
            if ($futureReservation->getHotelGroup() === $this) {
                $futureReservation->setHotelGroup(null);
            }
        }

        return $this;
    }

    public function getUtfOffset(): ?string
    {
        return $this->UtfOffset;
    }

    public function setUtfOffset(?string $UtfOffset): self
    {
        $this->UtfOffset = $UtfOffset;

        return $this;
    }

    public function getCheckoutIsLocked(): ?bool
    {
        return $this->checkoutIsLocked;
    }

    public function setCheckoutIsLocked(?bool $checkoutIsLocked): self
    {
        $this->checkoutIsLocked = $checkoutIsLocked;

        return $this;
    }

    public function getMh3AsProxyV2(): ?bool
    {
        return $this->Mh3AsProxyV2;
    }

    public function setMh3AsProxyV2(bool $Mh3AsProxyV2): self
    {
        $this->Mh3AsProxyV2 = $Mh3AsProxyV2;

        return $this;
    }

    public function getTitanReservationUrl(): ?string
    {
        return $this->titanReservationUrl;
    }

    public function setTitanReservationUrl(?string $titanReservationUrl): static
    {
        $this->titanReservationUrl = $titanReservationUrl;

        return $this;
    }

    public function getCreationDateReservationsUrl(): ?string
    {
        return $this->CreationDateReservationsUrl;
    }

    public function setCreationDateReservationsUrl(?string $CreationDateReservationUrl): static
    {
        $this->CreationDateReservationsUrl = $CreationDateReservationUrl;

        return $this;
    }

    public function isCreationDateIntegrationInProgress(): ?bool
    {
        return $this->CreationDateIntegrationInProgress;
    }

    public function setCreationDateIntegrationInProgress(?bool $CreationDateIntegrationInProgress): static
    {
        $this->CreationDateIntegrationInProgress = $CreationDateIntegrationInProgress;

        return $this;
    }

    public function getTenant(): ?string
    {
        return $this->tenant;
    }

    public function setTenant(?string $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getDaysAhead(): ?string
    {
        return $this->daysAhead;
    }

    public function setDaysAhead(?string $daysAhead): static
    {
        $this->daysAhead = $daysAhead;

        return $this;
    }
}
