<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 7:12 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types;

use ARTulloss\Hotbar\Types\Traits\CommandTrait;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
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

    /** @var string $defaults */
    private $defaults;

    /**
     * @param Player $player
     * @param int $slot
     */
    public function execute(Player $player, int $slot): void{
        $server = $player->getServer();
        $commands = $this->getSlotCommands($slot);
        if($commands !== null) {
            foreach ($commands as $command) {
                $commandData = explode('@', $command);
                if (isset($commandData[0])) {
                    $level = $player->getLevel();
                    $command = $this->substituteString($commandData[0], [
                        'player' => $player->getName(),
                        'tag' => $player->getNameTag(),
                        'level' => $level !== null ? $level->getName() : 'Error',
                        'x' => $player->getX(),
                        'y' => $player->getY(),
                        'z' => $player->getZ()
                    ], '{', '}');

                    if(!isset($commandData[1])) {
                        if(!isset($this->defaults))
                            $this->defaults = $server->getPluginManager()->getPlugin('Hotbar')->getConfig()->get('Default Command Options');
                        $commandData[1] = $this->defaults;
                    }
                    $executor = strtolower($commandData[1]);
                    switch ($executor) {
                        case 'console':
                            $server->dispatchCommand(new ConsoleCommandSender(), $command);
                            break;
                        case 'op':
                            $opStatus = $player->isOp();
                            $player->setOp(true);
                        case 'player':
                            $server->dispatchCommand($player, $command);
                            if(isset($opStatus) && $opStatus !== true)
                                $player->setOp(false);
                            break;
                        default:
                            $server->getPluginManager()->getPlugin('Hotbar')->getLogger()->error("Invalid executor $executor! Please remove the @$executor or replace $executor with player, op or server!");
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
            $string = str_ireplace($prefix . $replaceMe . $suffix, $with, $string);
        }
        return $string;
    }

}