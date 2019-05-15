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
use pocketmine\level\Level;

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
    /** @var Main $main */
    private $main;
    /** @var HotbarInterface $levelHotbars */
    private $levelHotbars;

    /**
     * HotbarLevels constructor.
     * @param Main $main
     */
    public function __construct(Main $main) {
        $this->main = $main;
    }
    /**
     * @param Level $level
     * @param HotbarInterface $hotbar
     */
    public function bindLevelToHotbar(Level $level, HotbarInterface $hotbar): void{
        $this->levelHotbars[$level->getName()] = $hotbar;
    }
    /**
     * @param Level $level
     * @return bool
     */
    public function unbindLevelToHotbar(Level $level): bool{
        $levelName = $level->getName();
        $return = isset($this->levelHotbars[$levelName]);
        unset($this->levelHotbars[$levelName]);
        return $return;
    }
    /**
     * @param Level $level
     * @return HotbarInterface|null
     */
    public function getHotbarForLevel(Level $level): ?HotbarInterface{
        $levelName = $level->getName();
        if(isset($this->levelHotbars[$levelName]))
            return $this->levelHotbars[$levelName];
        return null;
    }
}