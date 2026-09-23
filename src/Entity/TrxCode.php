<?php

namespace App\Entity;

use App\Repository\TrxCodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrxCodeRepository")
 */
#[ORM\Entity(repositoryClass: TrxCodeRepository::class)]
class TrxCode
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
     * @ORM\ManyToOne(targetEntity="App\Entity\HotelGroup", inversedBy="trxCodes")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: HotelGroup::class, inversedBy: "trxCodes")]
    #[ORM\JoinColumn(nullable: false)]
    private ?HotelGroup $hotelGroup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $resort;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private ?int $created;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: 'integer',nullable: true)]
    private ?int $modified;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $trxCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $transactionCategory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $tcGroup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $tcSubgroup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $d1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $d2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $d3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adjTrx;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setResort(string $resort): self
    {
        $this->resort = $resort;

        return $this;
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

    public function getTrxCode(): ?string
    {
        return $this->trxCode;
    }

    public function setTrxCode(string $trxCode): self
    {
        $this->trxCode = $trxCode;

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

    public function getTcGroup(): ?string
    {
        return $this->tcGroup;
    }

    public function setTcGroup(?string $tcGroup): self
    {
        $this->tcGroup = $tcGroup;

        return $this;
    }

    public function getTcSubgroup(): ?string
    {
        return $this->tcSubgroup;
    }

    public function setTcSubgroup(?string $tcSubgroup): self
    {
        $this->tcSubgroup = $tcSubgroup;

        return $this;
    }
    
    public function getD1(): ?string
    {
        return $this->d1;
    }

    public function setD1(?string $d1): self
    {
        $this->d1 = $d1;

        return $this;
    }

    public function getD2(): ?string
    {
        return $this->d2;
    }

    public function setD2(?string $d2): self
    {
        $this->d2 = $d2;

        return $this;
    }

    public function getD3(): ?string
    {
        return $this->d3;
    }

    public function setD3(?string $d3): self
    {
        $this->d3 = $d3;

        return $this;
    }

    public function getAdjTrx(): ?string
    {
        return $this->adjTrx;
    }

    public function setAdjTrx(?string $adjTrx): self
    {
        $this->adjTrx = $adjTrx;

        return $this;
    }
}
