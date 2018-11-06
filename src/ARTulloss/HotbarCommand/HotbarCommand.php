<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 11/1/2018
 * Time: 9:11 PM
 */
namespace ARTulloss\HotbarCommand;

use ARTulloss\Hotbar\Hotbar;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class HotbarCommand extends Command implements PluginIdentifiableCommand {

	public static $instance;

	/**
	 * @return HotbarCommand
	 */
	public static function getInstance(): HotbarCommand{
		return self::$instance;
	}

	/**
	 * HotbarCommand constructor.
	 */
	public function __construct() {

		parent::__construct("hotbar", "For making multiple Hotbars on a world!", null, []);

		self::$instance = $this;

	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) :void {

		if($sender instanceof Player) {
			$sender->sendMessage(TextFormat::BLUE . "Hotbar v" . Hotbar::VERSION . " by ARTulloss");
			return;
		}

		if(!isset($args[0]) || !isset($args[1])){
			$this->getPlugin()->getServer()->getLogger()->notice("Missing an argument");
			return;
		}

		$player = $this->getPlugin()->getServer()->getPlayer($args[1]);

		if(!$player instanceof Player){
			$this->getPlugin()->getServer()->getLogger()->notice("Player not found");
			return;
		}

		// Checks for WORLD in /hotbar WORLD {player}  and gives player the items for their current world

		if($args[0] == "WORLD") {
			$level = $player->getLevel()->getName();
			Hotbar::getInstance()->setUsing($player->getName(), $level . ":" . "Worlds");
			Hotbar::getInstance()->sendItems($player, "Worlds", $level);
			return;
		}

		// Checks for the hotbar

		foreach (Hotbar::getInstance()->getItems() as $name => $data){
			if($args[0] == $name) {
				Hotbar::getInstance()->setUsing($player->getName(), $name . ":" . "Secondary-Hotbars");
				Hotbar::getInstance()->sendItems($player, "Secondary-Hotbars", $name);
				return;
			}
		}

		$this->getPlugin()->getServer()->getLogger()->notice("Hotbar " . $args[0] . " not found");

	}

	/**
	 * @return Plugin
	 */
	public function getPlugin(): Plugin {
		return Hotbar::getInstance();
	}

}