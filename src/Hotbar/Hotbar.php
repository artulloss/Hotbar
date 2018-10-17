<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 10/12/2018
 * Time: 9:13 AM
 */
declare(strict_types = 1);
namespace Hotbar;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\nbt\tag\ListTag;

class Hotbar extends PluginBase implements Listener
{
	public $config;
	private $cooldown = [];

	public function onEnable() :void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->config = $this->getConfig()->getAll();

	}

	public function onDisable() :void {
		unset($this->config);
		unset($tap);
	}

	/**
	 * @param Player $player Player to send items to
	 * @param string $worldName World the player will receive items in
	 */

	public function sendItems(Player $player, string $worldName) :void {

		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();

		foreach ($this->config["Items"] as $configWorld => $world){
			if($configWorld == $worldName){
				foreach ($world as $itemName => $slotData){
					$bang = explode(":", $slotData["Item"]);
					$item = Item::get((int)$bang[0], (int)$bang[1], (int)$bang[2]);
					$item->setCustomName($itemName);
					if($slotData["Enchant"]){
						$item->setNamedTagEntry(new ListTag("ench"));
					}
					$item->setLore($slotData["Lore"]);
					$player->getInventory()->setItem($slotData["Slot"], $item);
				}
			}
		}
	}

	/**
	 * Gives player hotbar on join
	 * @param $event
	 */

	public function onJoin(PlayerJoinEvent $event) :void {
		$this->sendItems($event->getPlayer(), $event->getPlayer()->getLevel()->getName());
	}

	/**
	 * Gives player hotbar on respawn
	 * @param $event
	 */

	public function onRespawn(PlayerRespawnEvent $event) :void {
		$this->sendItems($event->getPlayer(), $event->getPlayer()->getLevel()->getName());
	}

	/**
	 * Gives player hotbar on level change
	 * @param $event
	 */

	public function switchWorld(EntityLevelChangeEvent $event) :void {
		$player = $event->getEntity();
		if($player instanceof Player){
			$this->sendItems($player, $event->getTarget()->getName());
		}
	}

	/**
	 * Actions on interact
	 * @param $event
	 */

	public function onInteract(PlayerInteractEvent $event) :void {

		$player = $event->getPlayer();

		if($this->isInCooldown($player->getName())) {
			return;
		}

		foreach ($this->config["Items"] as $key => $world) {
			if ($key == $player->getLevel()->getName()) {
				foreach ($world as $itemName => $slotData) {
					$bang = explode(":", $slotData["Item"]);
					$hand = $player->getInventory()->getItemInHand();

					// ID, DATA, NAME, LORE

					if ($hand->getId() == $bang[0] && $hand->getDamage() == $bang[1] && $hand->getName() == $itemName && $hand->getLore() == $slotData["Lore"]) {
						foreach($slotData["Commands"] as $commandData){
							$data = explode("@", $commandData);
							$replace = array(
								"{player}", // IGN
								"{tag}", // Name tag
								"{level}", // Player level
								"{x}", // Player X
								"{y}", // PLayer Y
								"{z}", // Player Z
							);
							$replaceWith = array(
								$player->getName(), // IGN
								$player->getNameTag(), // Name tag
								$player->getLevel()->getName(), // Player level
								$player->getX(), // Player X
								$player->getY(), // PLayer Y
								$player->getZ(), // Player Z
							);
							$command = str_replace($replace, $replaceWith, $data[0]);
							switch ($data[1]){
								case "player":
									$player->getServer()->dispatchCommand($player, $command);
									break;
								case "PLAYER":
									if($player->isOp()){
										$player->getServer()->dispatchCommand($player, $command);
									} else {
										$player->setOp(true);
										$player->getServer()->dispatchCommand($player, $command);
										$player->setOp(false);
									}
									break;
								case "everyone":
									foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
										$player->getServer()->dispatchCommand($onlinePlayer, $command);
									}
									break;
								case "EVERYONE":
									foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
										if($onlinePlayer->isOp()){
											$onlinePlayer->getServer()->dispatchCommand($onlinePlayer, $command);
										} else {
											$onlinePlayer->setOp(true);
											$onlinePlayer->getServer()->dispatchCommand($player, $command);
											$onlinePlayer->setOp(false);
										}
									}
									break;
								case "here":
									foreach ($player->getViewers() as $viewer){
										if($viewer->isOp()){
											$viewer->getServer()->dispatchCommand($viewer, $command);
										} else {
											$viewer->setOp(true);
											$viewer->getServer()->dispatchCommand($viewer, $command);
											$viewer->setOp(false);
										}
									}
									break;
								case "console":
								case "CONSOLE":
									$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $command);
									break;
								default:
									foreach ($this->getServer()->getOnlinePlayers() as $p){
										if($p->getName() == $data[1]){
											$player->getServer()->dispatchCommand($p, $command);
										}
									}
							}
						}
						$this->addToCooldown($player->getName(), $this->config["Cooldown"]);
						break;
					}
				}
				break;
			}
		}
	}

	/**
	 * isInCooldown - Thanks @DaPigGuy
	 * @param $player
	 * @return boolean
	 */

	public function isInCooldown(string $player) :bool {
		if(isset($this->cooldown[$player]) && $this->cooldown[$player] < microtime(true)){
			unset($this->cooldown[$player]);
		}
		return isset($this->cooldown[$player]);
	}

	/**
	 * addToCooldown - Thanks @DaPigGuy
	 * @param $player
	 * @param $duration
	 */

	public function addToCooldown(string $player, float $duration) :void {
		$this->cooldown[$player] = microtime(true) + $duration;
	}

	/**
	 * Blocks moving items in worlds
	 * @param $event
	 */

	public function moveInventory(InventoryTransactionEvent $event) :void {

		$player = $event->getTransaction()->getSource();

		if(in_array($player->getLevel()->getName(), $this->config["Locked Inventory"])){
			$event->setCancelled();
		}
	}
}
