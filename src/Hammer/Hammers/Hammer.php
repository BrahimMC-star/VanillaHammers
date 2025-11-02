<?php

namespace Hammer\Hammers;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\Pickaxe as PickaxePM;
use pocketmine\item\ToolTier as ToolTierPM;

class Hammer extends PickaxePM
{
    public function __construct(ItemIdentifier $identifier, string $name, protected ToolTier $ctier, array $enchantmentTags = [])
    {
        parent::__construct($identifier, $name, ToolTierPM::DIAMOND(), $enchantmentTags);
    }

    public function getMaxDurability(): int
    {
        return $this->ctier->getMaxDurability();
    }

    public function getAttackPoints(): int
    {
        return $this->ctier->getBaseAttackPoints() - 2;
    }

    protected function getBaseMiningEfficiency(): float
    {
        return $this->ctier->getBaseEfficiency();
    }

    public function getEnchantability(): int
    {
        return $this->ctier->getEnchantability();
    }
    public function getToolTier(): ToolTierPM
    {
        return ToolTierPM::DIAMOND();
    }
}
