<?php

namespace Hammer\Listener;

use Hammer\Hammers\Hammer;
use pocketmine\block\Air;
use pocketmine\block\Bedrock;
use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\block\Dirt;
use pocketmine\block\GlowingObsidian;
use pocketmine\block\Grass;
use pocketmine\block\Gravel;
use pocketmine\block\Liquid;
use pocketmine\block\Sand;
use pocketmine\block\Wood;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;

final class HammerListener implements Listener
{
    /** @var array<string,bool> */
    private static array $breakingGuard = [];

    public function onBlockBreak(BlockBreakEvent $event) : void
    {
        $player = $event->getPlayer();
        $item   = $event->getItem();

        if (!$item instanceof Hammer) {
            return;
        }

        if ($event->isCancelled()) {
            return;
        }

        $pid = $player->getUniqueId()->toString();
        if (isset(self::$breakingGuard[$pid])) {
            return;
        }

        $block  = $event->getBlock();
        $world  = $block->getPosition()->getWorld();
        $loc    = $player->getLocation();
        $pitch  = $loc->pitch;
        $yaw    = $loc->yaw;

        $width  = $item->getCustomToolTier()->getMiningAreaWidth();
        $height = $item->getCustomToolTier()->getMiningAreaHeight();
        $rangeW = $this->axisRange($width);
        $rangeH = $this->axisRange($height);

        $offsets = [];
        if (abs($pitch) > 60) {
            foreach ($rangeW as $dx) {
                foreach ($rangeH as $dz) {
                    if ($dx === 0 && $dz === 0) continue;
                    $offsets[] = new Vector3($dx, 0, $dz);
                }
            }
        } else {
            $normYaw = fmod($yaw, 360.0);
            if ($normYaw < 0) $normYaw += 360.0;

            $distToZAxis = min(abs($normYaw), abs($normYaw - 180));
            $useYZ = $distToZAxis > 45 && $distToZAxis < 135;

            if ($useYZ) {
                foreach ($rangeH as $dy) {
                    foreach ($rangeW as $dz) {
                        if ($dy === 0 && $dz === 0) continue;
                        $offsets[] = new Vector3(0, $dy, $dz);
                    }
                }
            } else {
                foreach ($rangeH as $dy) {
                    foreach ($rangeW as $dx) {
                        if ($dy === 0 && $dx === 0) continue;
                        $offsets[] = new Vector3($dx, $dy, 0);
                    }
                }
            }
        }

        $center = $block->getPosition();

        self::$breakingGuard[$pid] = true;
        try {
            foreach ($offsets as $off) {
                $pos = $center->addVector($off);
                $b   = $world->getBlock($pos);
                if (!$this->canBreakWithHammer($b)) {
                    continue;
                }
                $world->useBreakOn($pos, $item, $player, true);
            }
        } finally {
            unset(self::$breakingGuard[$pid]);
        }
    }

    /**
     * @return int[]
     */
    private function axisRange(int $size) : array
    {
        $size = max(1, $size);
        $before = intdiv($size - 1, 2);
        $after  = $size - 1 - $before;
        return range(-$before, $after);
    }

    private function canBreakWithHammer(Block $block) : bool
    {
        $cannotBreak = match (true) {
            $block instanceof Air,
            $block instanceof Liquid,
            $block instanceof Bedrock,
            $block instanceof GlowingObsidian,
            $block instanceof Dirt,
            $block instanceof Grass,
            $block instanceof Gravel,
            $block instanceof Sand,
            $block instanceof Wood => true,
            default => false
        };
        if ($cannotBreak) {
            return false;
        }
        return $block->getBreakInfo()->getToolType() === BlockToolType::PICKAXE;
    }
}
