<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 10/12/2018
 * Time: 9:13 AM
 */
declare(strict_types = 1);

namespace ARTulloss\Hotbar;

use ARTulloss\Hotbar\Accessor\HotbarLevels;
use ARTulloss\Hotbar\Accessor\HotbarUserAccessor;
use ARTulloss\Hotbar\Command\HotbarCommand;
use ARTulloss\Hotbar\Events\Listener;
use ARTulloss\Hotbar\Factory\HotbarFactory;
use ARTulloss\Hotbar\Types\CommandHotbar;
use ARTulloss\Hotbar\Types\HotbarInterface;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use function explode;
use function key;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class Main extends PluginBase
{
	public const VERSION = "2.0.0";
	public const CONFIG_VERSION = 2.0;

	/** @var HotbarLevels $hotbarLevels */
	private $hotbarLevels;
	/** @var HotbarUserAccessor */
	private $hotbarUsers;
	/** @var HotbarInterface[] $hotbars */
	private $hotbars;

	public function onEnable(): void {
		$this->saveDefaultConfig();
        $server = $this->getServer();
        if($this->getConfig()->get('Config Version') !== self::VERSION) {
		    $this->getLogger()->info('Hotbar config does not match the required version! Please update your configuration!');
		    $server->getPluginManager()->disablePlugin($this);
        }
		$server->getCommandMap()->register("hotbar", new HotbarCommand('hotbar', $this));
		$server->getPluginManager()->registerEvents(new Listener($this), $this);
		$this->hotbarLevels = new HotbarLevels($this);
		$this->hotbarUsers = new HotbarUserAccessor();
		$this->registerHotbars();
		$this->registerHotbarWorlds();
	}
	public function registerHotbars(): void{
	    foreach ($this->getConfig()->get('Hotbars') as $hotbarName => $hotbarData) {
	        $itemName = key($hotbarData);
	        $hotbarData = $hotbarData[$itemName];
            $itemArray = explode(':', $hotbarData['Item']);

            if(!isset($itemArray[0]) || !isset($itemArray[1]) || !isset($itemArray[2])) {
                $this->getLogger()->error("Detected malformed item in $hotbarName hotbar! Make sure that you use the format ID:META:COUNT.");
                continue;
            }
            $item = Item::get((int)$itemArray[0], (int)$itemArray[1], (int)$itemArray[2]);
            $item->setCustomName($itemName);
            $items[$hotbarData['Slot']] = $item;
            $commands = $hotbarData['Commands'];
            $slot = $hotbarData['Slot'];
            $hotbarCommands[$slot] = $commands;
        }
        if(isset($items))
            if(isset($hotbarName)) {
                $this->hotbars[$hotbarName] = HotbarFactory::make('command', $items);
                foreach ($hotbarCommands ?? [] as $slot => $commands) {
                    /** @var CommandHotbar $hotbar */
                    $hotbar = $this->hotbars[$hotbarName];
                    $hotbar->setSlotCommands($slot, $commands);
                }
            } else
                $this->getLogger()->error('Undefined hotbar name!');
        else
            $this->getLogger()->error('Detected empty hotbar! If you want to clear a players inventory please use the /hotbar clear command');
    }
    public function registerHotbarWorlds(): void{
	    foreach ($this->getConfig()->get('Worlds') as $levelName => $hotbarName) {
	        $level = $this->getServer()->getLevelByName($levelName);
	        if($level !== null)
	            if(isset($this->hotbars[$hotbarName]))
	                $this->getHotbarLevels()->bindLevelToHotbar($level, $this->hotbars[$hotbarName]);
	            else
	                $this->getLogger()->notice("Tried to bind hotbar $hotbarName to world but $hotbarName isn't defined!");
	        else
	            $this->getLogger()->error("Invalid level $levelName paired with hotbar $hotbarName");
        }
    }
    /**
     * @return HotbarLevels
     */
    public function getHotbarLevels(): HotbarLevels{
	    return $this->hotbarLevels;
    }
    /**
     * @return HotbarUserAccessor
     */
    public function getHotbarUsers(): HotbarUserAccessor{
        return $this->hotbarUsers;
    }
    /**
     * @return HotbarInterface[]
     */
    public function getHotbars(): array{
        return $this->hotbars;
    }
}
