<?php

namespace Hammer\Hammers;

readonly class ToolTier
{

    public function __construct(
        private int $harvestLevel,
        private int $maxDurability,
        private int $baseAttackPoints,
        private int $baseEfficiency,
        private int $enchantability
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
        return $this->baseAttackPoints;
    }

    /**
     * @return int
     */
    public function getBaseEfficiency(): int
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
}