<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'stocks')]
final class Stock
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[Column(type: 'string', nullable: true)]
    protected string $name;

    #[Column(type: 'float', nullable: true)]
    protected float $close;

    #[Column(type: 'string', nullable: true)]
    protected string $symbol;

    #[Column(type: 'float', nullable: true)]
    protected float $open;

    #[Column(type: 'float', nullable: true)]
    protected float $high;

    #[Column(type: 'float', nullable: true)]
    protected float $low;

    #[Column(name: 'date', type: 'string', nullable: true)]
    protected string $date;

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param float $close
     */
    public function setClose(float $close): void
    {
        $this->close = $close;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    /**
     * @param float $open
     */
    public function setOpen(float $open): void
    {
        $this->open = $open;
    }

    /**
     * @param float $high
     */
    public function setHigh(float $high): void
    {
        $this->high = $high;
    }

    /**
     * @param float $low
     */
    public function setLow(float $low): void
    {
        $this->low = $low;
    }

    /**
     * @param string $date
     * @param string $time
     * @return void
     */
    public function setDate(string $date, string $time): void
    {
        $this->date = (new \DateTime("{$date} {$time}"))
            ->format(DATE_W3C);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getClose(): ?float
    {
        return $this->close;
    }

    public function getHigh(): ?float
    {
        return $this->high;
    }

    public function getOpen(): ?float
    {
        return $this->open;
    }

    public function getLow(): ?float
    {
        return $this->low;
    }
}