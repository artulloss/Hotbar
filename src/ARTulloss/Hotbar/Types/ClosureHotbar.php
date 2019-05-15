<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 7:12 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types;

use ARTulloss\Hotbar\Types\Traits\ClosureTrait;
use pocketmine\Player;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class ClosureHotbar extends Hotbar
{
    use ClosureTrait;
    /**
     * @param Player $player
     * @param int $slot
     */
    public function execute(Player $player, int $slot): void{
        $this->executeClosure($player, $slot);
    }
}