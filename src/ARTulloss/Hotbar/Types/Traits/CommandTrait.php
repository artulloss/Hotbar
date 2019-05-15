<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 7:14 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types\Traits;

use pocketmine\Server;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

trait CommandTrait
{
    /** @var string[] $commands */
    private $commands;
    /**
     * @param int $slot
     * @param string[] $commands
     */
    public function setSlotCommands(int $slot, array $commands): void{
        if($slot < 1 || $slot > 9)
            Server::getInstance()->getPluginManager()->getPlugin('Hotbar')->getLogger()->error(self::INVALID_SLOT);
        $this->commands[$slot] = $commands;
    }
    /**
     * @return array
     */
    public function getCommands(): array{
        return $this->commands;
    }
    /**
     * @param int $slot
     * @return array|null
     */
    public function getSlotCommands(int $slot): ?array{
        $slot++; // Adjust the slot
        if(isset($this->commands[$slot]))
            return $this->commands[$slot];
        return null;
    }
    /**
     * @param int $slot
     * @return bool
     */
    public function hasCommands(int $slot): bool{
        return isset($this->commands[$slot]);
    }
}