<?php

namespace Hammer\Hammers;

readonly class ToolTier
{

    public function __construct(
        private int $harvestLevel,
        private int $maxDurability,
        private int $baseAttackPoints,
        private float $baseEfficiency,
        private int $enchantability,
        private int $miningAreaWidth = 3,
        private int $miningAreaHeight = 3
    ){}

    /**
     * @return int
     */
    public function getHarvestLevel(): int
    {
        return $this->harvestLevel;
    }

    /**
     * @return int
     */
    public function getMaxDurability(): int
    {
        return $this->maxDurability;
    }

    /**
     * @return int
     */
    public function getBaseAttackPoints(): int
    {
        return $this->baseAttackPoints - 2;
    }

    /**
     * @return float
     */
    public function getBaseEfficiency(): float
    {
        return $this->baseEfficiency;
    }

    /**
     * @return int
     */
    public function getEnchantability(): int
    {
        return $this->enchantability;
    }

    public function getMiningAreaWidth(): int
    {
        return $this->miningAreaWidth;
    }

    public function getMiningAreaHeight(): int
    {
        return $this->miningAreaHeight;
    }
}
