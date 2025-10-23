<?php

namespace Hammer\Listener;

use Hammer\Hammers\Hammer;
use pocketmine\block\Air;
use pocketmine\block\Bedrock;
use pocketmine\block\Block;
use pocketmine\block\Dirt;
use pocketmine\block\GlowingObsidian;
use pocketmine\block\Gravel;
use pocketmine\block\Liquid;
use pocketmine\block\Sand;
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

        $offsets = [];
        if (abs($pitch) > 60) {
            for ($dx = -1; $dx <= 1; $dx++) {
                for ($dz = -1; $dz <= 1; $dz++) {
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
                for ($dy = -1; $dy <= 1; $dy++) {
                    for ($dz = -1; $dz <= 1; $dz++) {
                        if ($dy === 0 && $dz === 0) continue;
                        $offsets[] = new Vector3(0, $dy, $dz);
                    }
                }
            } else {
                for ($dy = -1; $dy <= 1; $dy++) {
                    for ($dx = -1; $dx <= 1; $dx++) {
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

    private function canBreakWithHammer(Block $block) : bool
    {
        if ($block instanceof Air) {
            return false;
        }
        if ($block instanceof Liquid) {
            return false;
        }
        if ($block instanceof Bedrock) {
            return false;
        }
        if ($block instanceof GlowingObsidian) {
            return false;
        }
        if ($block instanceof Dirt) {
            return false;
        }
        if ($block instanceof Gravel) {
            return false;
        }
        if ($block instanceof Sand) {
            return false;
        }
        return true;
    }
}