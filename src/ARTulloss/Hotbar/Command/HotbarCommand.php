<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 11/21/2018
 * Time: 10:41 AM
 */
declare(strict_types = 1);

namespace ARTulloss\Hotbar\Command;

use ARTulloss\Hotbar\Events\LoseHotbarEvent;
use ARTulloss\Hotbar\Types\HotbarInterface;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use ARTulloss\Hotbar\Main;
use function count;


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

    private const MESSAGE = TextFormat::BLUE . 'Hotbar v' . Main::VERSION . ' by ARTulloss';

    /**
     * HotbarCommand constructor.
     * @param string $name
     * @param Plugin $owner
     */
    public function __construct(string $name, Plugin $owner) {
        parent::__construct($name, $owner);
        $this->setDescription(self::MESSAGE);
        $this->setUsage('/hotbar {clear} | {list} | {world} {player}');
    }
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @throws \ReflectionException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if($sender instanceof Player && !$sender->hasPermission('hotbar.view')) {
            $sender->sendMessage(self::MESSAGE);
            return;
        }
        $this->executePrivileged($sender, $args);
    }
    /**
     * @param CommandSender $sender
     * @param array $args
     * @throws \ReflectionException
     */
    public function executePrivileged(CommandSender $sender, array $args): void{
        $server = $sender->getServer();
        if(isset($args[0])) {

            /** @var Main $plugin */
            $plugin = $this->getPlugin();

            if(isset($args[1]))
                $player = $server->getPlayerExact($args[1]);

            if(isset($player) && $player !== null) {
                $hotbarUser = $plugin->getHotbarUsers()->getHotbarFor($player);
                switch ($args[0]) {
                    case '{world}':
                        $level = $player->getLevel();
                        $hotbar = $plugin->getHotbarLevels()->getHotbarForLevel($level);
                        $hotbarUser->setHotbar($hotbar);
                        break;
                    case '{clear}':
                        $player->getInventory()->clearAll();
                        $player->getArmorInventory()->clearAll();
                        $plugin->getHotbarUsers()->remove($player);
                        break;
                    case ($hotbars = $plugin->getHotbars()) && isset($hotbars[$args[0]]):
                        /** @var HotbarInterface $hotbar */
                        $hotbar = $hotbars[$args[0]];
                        $plugin->getHotbarUsers()->assign($player, $hotbar);
                        break;
                    default:
                        $sender->sendMessage(TextFormat::RED . "Unknown hotbar name $args[0]");
                }
                return;
            }
            switch ($args[0]) {
                case '{list}':
                    $hasHotbars = false;
                    $hotbarNames = array_keys($plugin->getHotbars());
                    if(count($hotbarNames) !== 0) {
                        $sender->sendMessage('Hotbars:');
                        foreach ($hotbarNames as $hotbarName)
                            $sender->sendMessage(TextFormat::BLUE . $hotbarName);
                        $hasHotbars = true;
                    }

                    $levelHotbars = $plugin->getHotbarLevels()->getAll();

                    if($levelHotbars !== null && count($levelHotbars) !== 0) {
                        $sender->sendMessage('Hotbars and Levels: ');
                        foreach ($levelHotbars as $levelName => $hotbar) {
                            $sender->sendMessage(TextFormat::BLUE . $hotbar->getName() . " on $levelName");
                        }
                        $hasHotbars = true;
                    }

                    if($hasHotbars)
                        return;
                    $sender->sendMessage('There are no hotbars registered!');
                    return;
                default:
                    if(isset($args[1]))
                        $sender->sendMessage(TextFormat::RED . "Player $args[1] not found!");
                    else
                        throw new InvalidCommandSyntaxException();
            }
        } else
            throw new InvalidCommandSyntaxException();
    }
}
