<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 10/12/2018
 * Time: 9:13 AM
 */
declare(strict_types = 1);
namespace ARTulloss\Hotbar;
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
	private $config;
	private $cooldown = [];

	public static $instance;

	public function onLoad(): void{
		self::$instance = $this;
	}

	public static function getInstance(): Hotbar{
		return self::$instance;
	}

	public function onEnable() :void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->config = $this->getConfig()->getAll();

	}

	public function onDisable() :void {
		unset($this->config);
		unset($cooldown);
		unset($this->ops);

	}

	/**
	 * @param Player $player Player to send items to
	 * @param string $worldName World the player will receive items in
	 */

	public function sendItems($player, string $worldName) :void {

		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		if(isset($this->config["Items"][$worldName])){
			foreach ($this->config["Items"][$worldName] as $itemName => $slotData){
				$bang = explode(":", $slotData["Item"]);
				$item = Item::get((int)$bang[0], (int)$bang[1], (int)$bang[2]);
				$item->setCustomName($itemName);
				if($slotData["Enchant"]){
					$item->setNamedTagEntry(new ListTag("ench"));
				}
				$item->setLore($slotData["Lore"]);
				$player->getInventory()->setItem(--$slotData["Slot"], $item);
			}
		}
	}

	/**
	 * Gives player Hotbar on join
	 * @param $event
	 */

	public function onJoin(PlayerJoinEvent $event) :void {
		$this->sendItems($event->getPlayer(), $event->getPlayer()->getLevel()->getName());
	}

	/**
	 * Gives player Hotbar on respawn
	 * @param $event
	 */

	public function onRespawn(PlayerRespawnEvent $event) :void {
		$this->sendItems($event->getPlayer(), $event->getPlayer()->getLevel()->getName());
	}

	/**
	 * Gives player Hotbar on level change
	 * @param $event
	 */

	public function switchWorld(EntityLevelChangeEvent $event) :void {
		$player = $event->getEntity();
		if($player instanceof Player) $this->sendItems($player, $event->getTarget()->getName());
	}

	/**
	 * Check if player is holding item in config
	 * @param $event
	 */

	public function onInteract(PlayerInteractEvent $event) :void
	{

		$player = $event->getPlayer();

		if ($this->isInCooldown($player->getName())) return;

		$playerWorld = $player->getLevel()->getName();

		if (isset($this->config["Items"][$playerWorld])) {

			$hand = $player->getInventory()->getItemInHand();

			if (isset($this->config["Items"][$playerWorld][$hand->getCustomName()])) {
				$bang = explode(":", $this->config["Items"][$playerWorld][$hand->getCustomName()]["Item"]);
				if ($hand->getId() == $bang[0] && $hand->getDamage() == $bang[1] && $hand->getLore() == $this->config["Items"][$playerWorld][$hand->getCustomName()]["Lore"])
					$this->interactAction($player->getName(), $hand);
			}
			return;
		}
	}

	/**
	 * Actions to do, if all checks out
	 *
	 * @param string $name
	 * @param Item $hand
	 */

	public function interactAction(string $name, Item $hand) :void {

		$player = $this->getServer()->getPlayer($name);

		$playerWorld = $player->getLevel()->getName();

		foreach ($this->config["Items"][$playerWorld][$hand->getCustomName()]["Commands"] as $commandData) {

			$command = explode("@", $commandData);

			$command = $this->replace($player, $command);

			$this->executeCommand((string)$name, (string)$command[0], (string)$command[1], (bool)$command[2]);

		}
		$this->addToCooldown($name, $this->config["Cooldown"]);
	}

	/**
	 * Replace data in command to match individual player
	 *
	 * @param $player
	 * @param array $command
	 * @return array
	 */

	public function replace($player, array $command) :array {

		$replace = array(
			"{player}", // IGN
			"{tag}", // Name tag
			"{level}", // Player level
			"{x}", // Player X
			"{y}", // PLayer Y
			"{z}", // Player Z
			"{rx}", // Player X (Rounded)
			"{ry}", // PLayer Y (Rounded)
			"{rz}", // Player Z (Rounded)
		);

		$replaceWith = array(
			$player->getName(), // IGN
			$player->getNameTag(), // Name tag
			$player->getLevel()->getName(), // Player level
			$player->getX(),
			$player->getY(),
			$player->getZ(),
			round($player->getX()), // Player X
			round($player->getY()), // PLayer Y
			round($player->getZ()), // Player Z
		);

		return str_replace($replace, $replaceWith, $command);

	}

	/**
	 * Executes commands
	 *
	 * @param string $name
	 * @param string $command
	 * @param string $sender
	 * @param bool $op
	 */

	public function executeCommand(string $name, string $command, string $sender, bool $op) :void {

		$player = $this->getServer()->getPlayer($name);

		$server = $this->getServer();

		switch(strtolower($sender)) {

			case "player":
				if($op && !$player->isOp()) {
					$player->setOp(true);
					$server->dispatchCommand($player, $command);
					$player->setOp(false);
					break;
				}
				$server->dispatchCommand($player, $command);
				break;
			case "here":
				foreach ($player->getViewers() as $viewer){
					if($op && !$viewer->isOp()) {
						$viewer->setOp(true);
						$server->dispatchCommand($viewer, $command);
						$viewer->setOp(false);
					} else
						$server->dispatchCommand($viewer, $command);
				}
				break;
			case "everyone":
				foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
					if($op && !$onlinePlayer->isOp()) {
						$onlinePlayer->setOp(true);
						$server->dispatchCommand($onlinePlayer, $command);
						$onlinePlayer->setOp(false);
					} else
						$server->dispatchCommand($onlinePlayer, $command);
				}
				break;
			case "console":
				$server->dispatchCommand(new ConsoleCommandSender(), $command);
				break;
			default:
			$p = $this->getServer()->getPlayer($sender);
			if($p) {
				if($op && !$p->isOp()) {
					$p->setOp(true);
					$server->dispatchCommand($p, $command);
					$p->setOp(false);
					break;
				}
				$server->dispatchCommand($p, $command);
			}
		}
	}

	/**
	 * Is the player in a cooldown? - Thanks @DaPigGuy for helping me with cooldowns!
	 *
	 * @param string $player
	 * @return bool
	 */

	public function isInCooldown(string $player) :bool {
		if(isset($this->cooldown[$player]) && $this->cooldown[$player] < microtime(true)) unset($this->cooldown[$player]);
		return isset($this->cooldown[$player]);
	}

	/**
	 * Add player to the cooldown
	 *
	 * @param string $player
	 * @param float $duration
	 */

	public function addToCooldown(string $player, float $duration) :void {
		$this->cooldown[$player] = microtime(true) + $duration;
	}

	/**
	 * Blocks moving items in specified worlds
	 *
	 * @param InventoryTransactionEvent $event
	 */

	public function moveInventory(InventoryTransactionEvent $event) :void {
		if(in_array($event->getTransaction()->getSource()->getLevel()->getName(), $this->config["Locked Inventory"])) $event->setCancelled();
	}
}
