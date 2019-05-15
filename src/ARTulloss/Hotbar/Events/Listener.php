<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 11/21/2018
 * Time: 11:34 AM
 */
declare(strict_types = 1);

namespace ARTulloss\Hotbar\Events;

use pocketmine\event\Listener as PMListener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Level;
use pocketmine\scheduler\ClosureTask;
use ARTulloss\Hotbar\Main;
use pocketmine\Player;
use function in_array;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class Listener implements PMListener
{
	/** @var Main $plugin */
	private $plugin;
	/**
	 * Observer constructor
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	/**
	 * Gives player Hotbar on join if they have one assigned
	 * @param $event
	 * @priority HIGHEST
	 */
	public function onJoin(PlayerJoinEvent $event): void{
		$player = $event->getPlayer();
		$level = $player->getLevel();
		$this->bindPlayerLevelHotbar($player, $level);
	}
	/**
     * Deregister them from plugin
	 * @param PlayerQuitEvent $event
	 * @priority HIGHEST
	 */
	public function onLeave(PlayerQuitEvent $event): void{
	    $player = $event->getPlayer();
	    $users = $this->plugin->getHotbarUsers();
	    if($users->getHotbarFor($player) !== null) {
	        $users->remove($player);
        }
	}
	/**
	 * Gives player Hotbar on respawn
	 * @param PlayerRespawnEvent $event
	 * @priority HIGHEST
	 */
	public function onRespawn(PlayerRespawnEvent $event): void{
	    $player = $event->getPlayer();
	    $level = $player->getLevel();
	    $this->bindPlayerLevelHotbar($player, $level);
	}
	/**
	 * Gives player Hotbar on level change
	 * @param $event
	 * @priority HIGHEST
	 */
	public function switchWorld(EntityLevelChangeEvent $event): void{
		$player = $event->getEntity();
		$level = $event->getTarget();
		if ($player instanceof Player){
		    $this->bindPlayerLevelHotbar($player, $level);
		}
	}
    /**
     * Sends a player the hotbar for a world, or not if none exists
     * @param Player $player
     * @param Level $level
     */
	private function bindPlayerLevelHotbar(Player $player, Level $level): void{
        $hotbar = $this->plugin->getHotbarLevels()->getHotbarForLevel($level);
        $users = $this->plugin->getHotbarUsers();
        if($hotbar !== null) {
            $users->assign($player, $hotbar);
        } else
            $users->remove($player);
    }
    /**
     * @param PlayerInteractEvent $event
     * @priority HIGHEST
     * @ignoreCancelled TRUE
     */
	public function onInteract(PlayerInteractEvent $event): void{
	    $player = $event->getPlayer();
	    $hotbarUser = $this->plugin->getHotbarUsers()->getHotbarFor($player);

	    if($hotbarUser !== null) {
            if($this->plugin->getServer()->getTick() - $hotbarUser->getLastUsage() <= $this->plugin->getConfig()->get('Cooldown')) {
                $event->setCancelled();
            } else {
                $hotbar = $hotbarUser->getHotbar();
                // Hack, remove in 4.0.0 ?
                $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function (int $currentTick) use ($hotbarUser, $player, $hotbar): void{
                    $hotbar->execute($player, $player->getInventory()->getHeldItemIndex());
                    (new UseHotbarEvent($hotbarUser, $player->getInventory()->getHeldItemIndex()))->call();
                }), 0);
                $hotbarUser->updateLastUsage();
            }
        }
	}
	/**
	 * Blocks moving items in specified worlds if the user is still assigned a hotbar
	 * @param InventoryTransactionEvent $event
	 */
	public function moveInventory(InventoryTransactionEvent $event): void{
	    $player = $event->getTransaction()->getSource();
	    $levelName = $player->getLevel()->getName();
		if (in_array($levelName, $this->plugin->getConfig()->get('Locked Inventory'), true) && $this->plugin->getHotbarUsers()->getHotbarFor($player))
		    $event->setCancelled();
	}
}
