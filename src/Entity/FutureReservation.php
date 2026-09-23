<?php

namespace App\Entity;

use App\Repository\FutureReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FutureReservationRepository")
 */
#[ORM\Entity(repositoryClass: FutureReservationRepository::class)]
#[ORM\Index(name: 'idx_reservation_id', fields: ['reservationId'])]
class FutureReservation
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
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $created;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reservationId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $confirmationNo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nameId;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $last;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $first;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $memberNo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $memberLevel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $vip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastStay;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $roomNo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $roomType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $adults;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $children;

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
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mobilePhone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nationality;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $birthdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $postalCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $country;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $rateCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $marketCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $sourceCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $travelAgentName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $companyName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $srcName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $preferences;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $remarks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resort;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HotelGroup", inversedBy="futureReservations")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: HotelGroup::class, inversedBy: "futureReservations")]
    #[ORM\JoinColumn(nullable: false)]
    private ?HotelGroup $hotelGroup;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CrsNo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PostingAllowedYN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CustomReference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $InsertDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(?int $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getReservationId(): ?int
    {
        return $this->reservationId;
    }

    public function setReservationId(?int $reservationId): self
    {
        $this->reservationId = $reservationId;

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

    public function getNameId(): ?int
    {
        return $this->nameId;
    }

    public function setNameId(?int $nameId): self
    {
        $this->nameId = $nameId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLast(): ?string
    {
        return $this->last;
    }

    public function setLast(?string $last): self
    {
        $this->last = $last;

        return $this;
    }

    public function getFirst(): ?string
    {
        return $this->first;
    }

    public function setFirst(?string $first): self
    {
        $this->first = $first;

        return $this;
    }

    public function getMemberNo(): ?string
    {
        return $this->memberNo;
    }

    public function setMemberNo(?string $memberNo): self
    {
        $this->memberNo = $memberNo;

        return $this;
    }

    public function getMemberLevel(): ?string
    {
        return $this->memberLevel;
    }

    public function setMemberLevel(?string $memberLevel): self
    {
        $this->memberLevel = $memberLevel;

        return $this;
    }

    public function getVip(): ?string
    {
        return $this->vip;
    }

    public function setVip(?string $vip): self
    {
        $this->vip = $vip;

        return $this;
    }

    public function getLastStay(): ?string
    {
        return $this->lastStay;
    }

    public function setLastStay(?string $lastStay): self
    {
        $this->lastStay = $lastStay;

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

    public function getRoomType(): ?string
    {
        return $this->roomType;
    }

    public function setRoomType(?string $roomType): self
    {
        $this->roomType = $roomType;

        return $this;
    }

    public function getAdults(): ?int
    {
        return $this->adults;
    }

    public function setAdults(?int $adults): self
    {
        $this->adults = $adults;

        return $this;
    }

    public function getChildren(): ?int
    {
        return $this->children;
    }

    public function setChildren(?int $children): self
    {
        $this->children = $children;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    public function setBirthdate(?string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getRateCode(): ?string
    {
        return $this->rateCode;
    }

    public function setRateCode(?string $rateCode): self
    {
        $this->rateCode = $rateCode;

        return $this;
    }

    public function getMarketCode(): ?string
    {
        return $this->marketCode;
    }

    public function setMarketCode(?string $marketCode): self
    {
        $this->marketCode = $marketCode;

        return $this;
    }

    public function getSourceCode(): ?string
    {
        return $this->sourceCode;
    }

    public function setSourceCode(?string $sourceCode): self
    {
        $this->sourceCode = $sourceCode;

        return $this;
    }

    public function getTravelAgentName(): ?string
    {
        return $this->travelAgentName;
    }

    public function setTravelAgentName(?string $travelAgentName): self
    {
        $this->travelAgentName = $travelAgentName;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getSrcName(): ?string
    {
        return $this->srcName;
    }

    public function setSrcName(?string $srcName): self
    {
        $this->srcName = $srcName;

        return $this;
    }

    public function getPreferences(): ?string
    {
        return $this->preferences;
    }

    public function setPreferences(?string $preferences): self
    {
        $this->preferences = $preferences;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    public function getCrsNo(): ?string
    {
        return $this->CrsNo;
    }

    public function setCrsNo(?string $CrsNo): static
    {
        $this->CrsNo = $CrsNo;

        return $this;
    }

    public function getPostingAllowedYN(): ?string
    {
        return $this->PostingAllowedYN;
    }

    public function setPostingAllowedYN(?string $PostingAllowedYN): static
    {
        $this->PostingAllowedYN = $PostingAllowedYN;

        return $this;
    }

    public function getCustomReference(): ?string
    {
        return $this->CustomReference;
    }

    public function setCustomReference(?string $CustomReference): static
    {
        $this->CustomReference = $CustomReference;

        return $this;
    }

    public function getInsertDate(): ?string
    {
        return $this->InsertDate;
    }

    public function setInsertDate(?string $InsertDate): static
    {
        $this->InsertDate = $InsertDate;

        return $this;
    }
}
