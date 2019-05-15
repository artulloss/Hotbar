<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 11/21/2018
 * Time: 10:41 AM
 */
declare(strict_types = 1);

namespace ARTulloss\Hotbar\Command;

use ARTulloss\Hotbar\Types\HotbarInterface;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use ARTulloss\Hotbar\Main;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class HotbarCommand extends PluginCommand {

    /**
     * HotbarCommand constructor.
     * @param string $name
     * @param Plugin $owner
     */
    public function __construct(string $name, Plugin $owner) {
        parent::__construct($name, $owner);
    }

    /**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void{

		if($sender instanceof Player) {
			$sender->sendMessage(TextFormat::BLUE . 'Hotbar v' . Main::VERSION . ' by ARTulloss');
		} else {
            $server = $sender->getServer();
            if(isset($args[0]) && isset($args[1])) {
                $player = $server->getPlayer($args[1]);
                if ($player !== null) {
                    /** @var Main $plugin */
                    $plugin = $this->getPlugin();
                    if(isset($hotbars[$args[0]]))
                        echo "\nSET";
                    if ($args[0] === '{world}') {
                        $level = $player->getLevel();
                        $hotbarUser = $plugin->getHotbarUsers()->getHotbarFor($player);
                        $hotbar = $plugin->getHotbarLevels()->getHotbarForLevel($level);
                        $hotbarUser->setHotbar($hotbar);
                    } elseif ($args[0] === '{clear}') {
                        $player->getInventory()->clearAll();
                        $player->getArmorInventory()->clearAll();
                        $plugin->getHotbarUsers()->remove($player);
                    } elseif (($hotbars = $plugin->getHotbars()) && isset($hotbars[$args[0]])) {
                        /** @var HotbarInterface $hotbar */
                        $hotbar = $hotbars[$args[0]];
                        $plugin->getHotbarUsers()->assign($player, $hotbar);
                    } else
                        $plugin->getLogger()->notice("Unknown hotbar name $args[0]");
                } else
                    $server->getLogger()->notice('Player ' . $args[1] . ' not found!');
            }
		}
	}
}