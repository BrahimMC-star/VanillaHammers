<?php

namespace Hammer;

use Hammer\Hammers\Hammer;
use Hammer\Hammers\ToolTier;
use Hammer\Items\HammerItems;
use Hammer\Listener\HammerListener;
use Nexly\Events\Impl\ItemRegistryEvent;
use Nexly\Events\Impl\RecipeRegistryEvent;
use Nexly\Events\NexlyEventManager;
use pocketmine\item\enchantment\ItemEnchantmentTags;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ResourcePack;
use pocketmine\resourcepacks\ResourcePackManager;
use pocketmine\utils\SingletonTrait;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Filesystem\Path;

class Main extends PluginBase
{
    use SingletonTrait;
    public function onEnable(): void
    {
        $this->registerListeners();
        $this->getLogger()->info("Vanilla Hammers enabled!");
        $this->loadResourcePack();
    }

    public function onLoad(): void
    {
        NexlyEventManager::getInstance()->listen(ItemRegistryEvent::class, static function (ItemRegistryEvent $ev): void {
            $woodenHammer = new ToolTier(0, 120, 4, 2, 20);
            $stoneHammer = new ToolTier(1, 262, 5, 4, 20);
            $ironHammer = new ToolTier(2, 500, 6, 6, 20);
            $diamondHammer = new ToolTier(3, 3122, 7, 8, 20);
            $goldenHammer = new ToolTier(0, 64, 3, 12, 20);

            $ev->register("hammer:wooden_hammer", new Hammer(new ItemIdentifier(ItemTypeIds::newId()), "Wooden Hammer", $woodenHammer, [ItemEnchantmentTags::PICKAXE]));
            $ev->register("hammer:stone_hammer", new Hammer(new ItemIdentifier(ItemTypeIds::newId()), "Stone Hammer", $stoneHammer, [ItemEnchantmentTags::PICKAXE]));
            $ev->register("hammer:iron_hammer", new Hammer(new ItemIdentifier(ItemTypeIds::newId()), "Iron Hammer", $ironHammer, [ItemEnchantmentTags::PICKAXE]));
            $ev->register("hammer:diamond_hammer", new Hammer(new ItemIdentifier(ItemTypeIds::newId()), "Diamond Hammer", $diamondHammer, [ItemEnchantmentTags::PICKAXE]));
            $ev->register("hammer:golden_hammer", new Hammer(new ItemIdentifier(ItemTypeIds::newId()), "Golden Hammer", $goldenHammer, [ItemEnchantmentTags::PICKAXE]));
            HammerItems::checkInit();
        });

        NexlyEventManager::getInstance()->listen(RecipeRegistryEvent::class, static function (RecipeRegistryEvent $ev): void {
            $shape = [
                "AAA",
                "ABA",
                " B "
            ];

            $parser = StringTOItemParser::getInstance();
            $ev->registerShaped($shape, ["A" => "diamond_block", "B" => "stick"], [$parser->parse("diamond_hammer")]);
            $ev->registerShaped($shape, ["A" => "gold_block", "B" => "stick"], [$parser->parse("golden_hammer")]);
            $ev->registerShaped($shape, ["A" => "iron_block", "B" => "stick"], [$parser->parse("iron_hammer")]);
            $ev->registerShaped($shape, ["A" => "cobblestone", "B" => "stick"], [$parser->parse("stone_hammer")]);
            $ev->registerShaped($shape, ["A" => "oak_log", "B" => "stick"], [$parser->parse("wooden_hammer")]);
            $ev->registerShaped($shape, ["A" => "spruce_log", "B" => "stick"], [$parser->parse("wooden_hammer")]);
            $ev->registerShaped($shape, ["A" => "birch_log", "B" => "stick"], [$parser->parse("wooden_hammer")]);
            $ev->registerShaped($shape, ["A" => "jungle_log", "B" => "stick"], [$parser->parse("wooden_hammer")]);
            $ev->registerShaped($shape, ["A" => "acacia_log", "B" => "stick"], [$parser->parse("wooden_hammer")]);
            $ev->registerShaped($shape, ["A" => "dark_oak_log", "B" => "stick"], [$parser->parse("wooden_hammer")]);
        });

        self::setInstance($instance = $this);
        $instance->saveResource("pack.zip");
    }

    private function loadResourcePack(): void
    {
        $manager = $this->getServer()->getResourcePackManager();

        try {
            $reflectionClass = new ReflectionClass(ResourcePackManager::class);
        } catch (ReflectionException $e) {
            $this->getLogger()->error("Failed to reflect ResourcePackManager: " . $e->getMessage());
            return;
        }

        $path = Path::join($this->getDataFolder(), "pack.zip");

        try {
            /** @var ResourcePack $pack */
            $pack = $reflectionClass->getMethod("loadPackFromPath")->invoke($manager, $path);
        } catch (ReflectionException $e) {
            $this->getLogger()->error("Failed to load resource pack: " . $e->getMessage());
            return;
        }

        $manager->setResourceStack(array_merge($manager->getResourceStack(), [$pack]));
    }

    public function registerListeners(): void
    {
        $list = $this->getServer()->getPluginManager();
        $list->registerEvents(new HammerListener(), $this);
    }
}
