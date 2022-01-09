<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 7:12 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types;

use ARTulloss\Hotbar\Main;
use ARTulloss\Hotbar\Types\Traits\CommandTrait;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use function str_ireplace;
use function strtolower;
use function explode;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class CommandHotbar extends Hotbar
{
    use CommandTrait;

    private string $defaults;

    /**
     * @param Player $player
     * @param int $slot
     */
    public function execute(Player $player, int $slot): void{
        $server = $player->getServer();
        /** @var Main $plugin */
        $plugin = $server->getPluginManager()->getPlugin('Hotbar');
        $commands = $this->getSlotCommands($slot);
        if($commands !== null) {
            foreach ($commands as $command) {
                $commandData = explode('@', $command);
                if (isset($commandData[0])) {
                    $level = $player->getWorld();
                    $command = $this->substituteString($commandData[0], [
                        'player' => '"' . $player->getName() . '"',
                        'tag' => $player->getNameTag(),
                        'level' => $level !== null ? $level->getDisplayName() : 'Error',
                        'x' => $player->getPosition()->getX(),
                        'y' => $player->getPosition()->getY(),
                        'z' => $player->getPosition()->getZ()
                    ], '{', '}');

                    if(!isset($commandData[1])) {
                        if(!isset($this->defaults))
                            $this->defaults = $plugin->getConfig()->get('Default Command Options');
                        $commandData[1] = $this->defaults;
                    }
                    $executor = strtolower($commandData[1]);
                    switch ($executor) {
                        case 'console':
                            $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), $command);
                            break;
                        case 'op':
                            $opStatus = $player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
                            $player->addAttachment($plugin, DefaultPermissions::ROOT_OPERATOR, true);
                        case 'player':
                            $server->dispatchCommand($player, $command);
                            if(isset($opStatus) && $opStatus !== true)
                                $player->addAttachment($plugin, DefaultPermissions::ROOT_OPERATOR, false);
                            break;
                        default:
                            $plugin->getLogger()->error("Invalid executor $executor! Please remove the @$executor or replace $executor with player, op or server!");
                    }
                }
            }
        }
    }
    /**
     * @param string $string
     * @param array $replace
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    public function substituteString(string $string, array $replace, string $prefix, string $suffix): string {
        foreach ($replace as $replaceMe => $with) {
            $string = str_ireplace($prefix . $replaceMe . $suffix, (string)$with, $string);
        }
        return $string;
    }

}
