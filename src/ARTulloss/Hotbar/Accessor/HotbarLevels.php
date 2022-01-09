<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 5/5/2019
 * Time: 9:14 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Accessor;

use ARTulloss\Hotbar\Main;
use ARTulloss\Hotbar\Types\HotbarInterface;
use pocketmine\world\World;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class HotbarLevels
{
    /** @var HotbarInterface[] $levelHotbars */
    private array $levelHotbars;

    /**
     * @param World $level
     * @param HotbarInterface $hotbar
     */
    public function bindLevelToHotbar(World $level, HotbarInterface $hotbar): void{
        $this->levelHotbars[$level->getDisplayName()] = $hotbar;
    }
    /**
     * @param World $level
     * @return bool
     */
    public function unbindLevelToHotbar(World $level): bool{
        $levelName = $level->getDisplayName();
        $return = isset($this->levelHotbars[$levelName]);
        unset($this->levelHotbars[$levelName]);
        return $return;
    }
    /**
     * @param World $level
     * @return HotbarInterface|null
     */
    public function getHotbarForLevel(World $level): ?HotbarInterface{
        $levelName = $level->getDisplayName();
        if(isset($this->levelHotbars[$levelName]))
            return $this->levelHotbars[$levelName];
        return null;
    }
    /**
     * @return HotbarInterface[]|null
     */
    public function getAll(): ?array{
        return $this->levelHotbars;
    }
}