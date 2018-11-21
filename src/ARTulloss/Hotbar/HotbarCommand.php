<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 11/21/2018
 * Time: 10:41 AM
 */

declare(strict_types = 1);
namespace ARTulloss\Hotbar;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 */

class HotbarCommand extends Command implements PluginIdentifiableCommand {

	private $plugin;

	/**
	 * HotbarCommand constructor.
	 * @param $hotbar
	 */
	public function __construct(Hotbar $hotbar) {
		parent::__construct("hotbar", "For making multiple Hotbars on a world!", null, []);
		$this->plugin = $hotbar;
	}

	public function getPlugin(): Plugin
	{
		return $this->plugin;
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {

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
			$this->plugin->setUsing($player->getName(), $level . ":" . "Worlds");
			$this->plugin->sendItems($player, "Worlds", $level);
			return;
		}

		// Checks for the hotbar

		foreach ($this->plugin->getItems() as $name => $data){
			if($args[0] == $name) {
				$this->plugin->setUsing($player->getName(), $name . ":" . "Secondary-Hotbars");
				$this->plugin->sendItems($player, "Secondary-Hotbars", $name);
				return;
			}
		}
		$this->getPlugin()->getServer()->getLogger()->notice("Hotbar " . $args[0] . " not found");
	}
}