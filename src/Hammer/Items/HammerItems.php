<?php

namespace Hammer\Items;

use Hammer\Hammers\Hammer;
use Nexly\Items\ItemBuilder;
use Nexly\Mappings\ItemMappings;
use pocketmine\utils\CloningRegistryTrait;
use Throwable;

/**
 * @method static Hammer WOODEN_HAMMER()
 * @method static Hammer STONE_HAMMER()
 * @method static Hammer IRON_HAMMER()
 * @method static Hammer GOLDEN_HAMMER()
 * @method static Hammer DIAMOND_HAMMER()
 */

class HammerItems
{
    use CloningRegistryTrait;

    protected static function setup(): void
    {
        foreach (ItemMappings::getInstance()->getMappings() as $mapping) {
            /** @var ItemBuilder $builder */
            $builder = $mapping["builder"];

            try {
                self::_registryRegister(str_replace(" ", "_", $builder->getItem()->getName()), $builder->getItem());
            } catch (Throwable) {
            }
        }
    }

    public static function checkInit() : void{
        if(self::$members === null){
            self::$members = [];
            self::setup();
        }
    }
}