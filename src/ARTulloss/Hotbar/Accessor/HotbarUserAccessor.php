<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/8/2019
 * Time: 9:43 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Accessor;

use ARTulloss\Hotbar\Events\LoseHotbarEvent;
use ARTulloss\Hotbar\HotbarUser;
use ARTulloss\Hotbar\Types\HotbarInterface;
use pocketmine\player\Player;
use ReflectionException;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class HotbarUserAccessor
{
    /** @var HotbarUser[] */
    private array $users;
    /**
     * @param Player $player
     * @param HotbarInterface $hotbar
     * @return HotbarUser
     */
    public function make(Player $player, HotbarInterface $hotbar): HotbarUser{
        $hotbarUser = new HotbarUser($player, $hotbar);
        $this->users[$player->getName()] = $hotbarUser;
        return $hotbarUser;
    }
    /**
     * @param Player $player
     * @param HotbarInterface $hotbar
     * @return HotbarUser
     */
    public function assign(Player $player, HotbarInterface $hotbar): HotbarUser{
        $hotbarUser = $this->getHotbarFor($player);
        if($hotbarUser === null)
            $hotbarUser = $this->make($player, $hotbar);
        $hotbarUser->setHotbar($hotbar);
        return $hotbarUser;
    }
    /**
     * Remove a hotbar user, clears inventory if they have a hotbar assigned
     * @param Player $player
     * @param bool $clear
     * @param bool $event
     * @return bool
     * @throws ReflectionException
     */
    public function remove(Player $player, $clear = true, $event = true): bool{
        $name = $player->getName();
        $return = isset($this->users[$name]);
        if($return) {
            if($clear)
                $player->getInventory()->clearAll();
            if($event)
                (new LoseHotbarEvent($this->users[$name]))->call();
        }
        unset($this->users[$name]);
        return $return;
    }
    /**
     * @param Player $player
     * @return HotbarUser|null
     */
    public function getHotbarFor(Player $player): ?HotbarUser{
        return $this->users[$player->getName()] ?? null;
    }
}