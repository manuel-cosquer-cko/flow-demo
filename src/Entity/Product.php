<?php

namespace App\Entity;

class Product {
    protected string $type;
    protected string $name;
    protected string $reference;
    protected int $price;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public static function generateTestProduct(): Product
    {
        return (new Product())
            ->setType('T-shirt')
            ->setPrice(28000)
            ->setName('T-shirt Checkout.com')
            ->setReference('RF35RXZ');
    }
}