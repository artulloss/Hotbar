<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 11/21/2018
 * Time: 11:34 AM
 */

declare(strict_types = 1);
namespace ARTulloss\Hotbar;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\scheduler\ClosureTask;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 */

/**
 * Class Observer
 * @package ARTulloss\Hotbar
 * @author ARTulloss
 */
class Observer implements Listener
{
	/** @var Hotbar $plugin */
	private $plugin;

	/**
	 * Observer constructor.
	 *
	 * @param Hotbar $plugin
	 */
	public function __construct(Hotbar $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * Gives player Hotbar on join and adds to array
	 *
	 * @param $event
	 * @priority HIGHEST
	 */
	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		$level = $player->getLevel()->getName();
		$this->plugin->setUsing($player->getName(), $level . ":" . "Worlds");
		$this->plugin->sendItems($player, "Worlds", $level);
	}

	/**
	 * Removes from array
	 *
	 * @param PlayerQuitEvent $event
	 * @priority HIGHEST
	 */
	public function onLeave(PlayerQuitEvent $event): void {
		unset($this->plugin->using[$event->getPlayer()->getName()]);
	}

	/**
	 * Gives player Hotbar on respawn
	 *
	 * @param PlayerRespawnEvent $event
	 * @priority HIGHEST
	 */
	public function onRespawn(PlayerRespawnEvent $event): void {
		$player = $event->getPlayer();
		$level = $event->getPlayer()->getLevel()->getName();
		$this->plugin->setUsing($player->getName(), $level . ":" . "Worlds");
		$this->plugin->sendItems($player, "Worlds", $level);
	}

	/**
	 * Gives player Hotbar on level change
	 *
	 * @param $event
	 * @priority HIGHEST
	 */
	public function switchWorld(EntityLevelChangeEvent $event): void {
		$player = $event->getEntity();
		$level = $event->getTarget()->getName();
		if ($player instanceof Player){
			$this->plugin->setUsing($player->getName(), $level . ":" . "Worlds");
			$this->plugin->sendItems($player, "Worlds", $level);
		}
	}

    /**
     * @param PlayerInteractEvent $event
     * @priority HIGHEST
     * @ignoreCancelled TRUE
     */
	public function onInteract(PlayerInteractEvent $event): void{
	    $player = $event->getPlayer();
	    $name = $player->getName();

	    if($this->plugin->isInCooldown($name)){
	        $event->setCancelled();
	        return;
	    }

        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function(int $currentTick)use($name): void{
            $this->plugin->interactFilter($name);
        }), 1);
	}

	/**
	 * Blocks moving items in specified worlds
	 *
	 * @param InventoryTransactionEvent $event
	 */
	public function moveInventory(InventoryTransactionEvent $event): void {
		if (\in_array($event->getTransaction()->getSource()->getLevel()->getName(), $this->plugin->config["Locked Inventory"])) $event->setCancelled();
	}
}
