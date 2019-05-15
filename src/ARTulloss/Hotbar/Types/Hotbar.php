<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 6:09 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types;

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use function array_keys;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

abstract class Hotbar implements HotbarInterface
{
    /** @var Item[] $items */
    private $items;
    /** @var string $name */
    private $name;
    protected const INVALID_SLOT = 'Slot must be between 1 and 9';

    /**
     * Hotbar constructor.
     * @param string $hotbar
     * @param Item[] $items - Send items in format of Slot => Item
     */
    public function __construct(string $hotbar, array $items) {
        foreach (array_keys($items) as $key) {
            $this->checkSlot($key);
        }
        $this->name = $hotbar;
        $this->items = $items;
    }
    /**
     * Validate if a slot is between the first and ninths slot
     * @param int $slot
     */
    protected function checkSlot(int $slot): void{
        if($slot < 1 || $slot > 9)
            Server::getInstance()->getPluginManager()->getPlugin('Hotbar')->getLogger()->error(self::INVALID_SLOT);
    }
    /**
     * @param Player $player
     */
    final public function send(Player $player): void{
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();

        $inventory = $player->getInventory();

        foreach ($this->getItems() as $slot => $item) {
            $item = clone $item; // Clear cached NBT data
            $inventory->setItem(--$slot, $item);
        }
    }
    /**
     * @param Item[] $items
     */
    public function setItems(array $items): void{
        $this->items = $items;
    }
    /**
     * @return Item[]
     */
    public function getItems(): array{
        return $this->items;
    }
    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }
}