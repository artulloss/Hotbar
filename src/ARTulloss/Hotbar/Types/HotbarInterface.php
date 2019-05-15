<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 8:45 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types;

use pocketmine\Player;
use pocketmine\item\Item;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

interface HotbarInterface
{
    /**
     * Send the hotbar to player
     * @param Player $player
     */
    public function send(Player $player): void;
    /**
     * Set the hotbars items
     * @param array $items
     */
    public function setItems(array $items): void;
    /**
     * Get the items in the hotbar
     * @return Item[]
     */
    public function getItems(): array;
    /**
     * @param Player $player
     * @param int $slot
     */
    public function execute(Player $player, int $slot): void;
    /**
     * @return string
     */
    public function getName(): string;
}