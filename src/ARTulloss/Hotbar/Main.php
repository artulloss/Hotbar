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
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
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

class Main extends PluginBase
{
	public const VERSION = '2.0.6';
	public const CONFIG_VERSION = '64GrpQ}g}gs4j1';

	private HotbarLevels $hotbarLevels;
	private HotbarUserAccessor $hotbarUsers;
	/** @var HotbarInterface[] $hotbars */
	private array $hotbars;

	public function onEnable(): void {
        $server = $this->getServer();
        if($this->getConfig()->get('Config Version') !== self::CONFIG_VERSION) {
		    $this->getLogger()->info('Hotbar config does not match the required version! Please update your configuration to continue using Hotbar!');
		    $server->getPluginManager()->disablePlugin($this);
		    return;
        }
		$server->getCommandMap()->register("hotbar", new HotbarCommand('hotbar', $this));
		$server->getPluginManager()->registerEvents(new Listener($this), $this);
		$this->hotbarLevels = new HotbarLevels();
		$this->hotbarUsers = new HotbarUserAccessor();
		$this->registerHotbars();
		$this->registerHotbarWorlds();
	}

	public function registerHotbars(): void{
	    $hotbars = $this->getConfig()->get('Hotbars');
	    foreach ($hotbars as $hotbarName => $hotbar) {
	        $items = [];
	        $hotbarCommands = [];
	        foreach ($hotbar as $itemName => $itemData) {
                $itemArray = explode(':', $itemData['Item']);

                if(!isset($itemArray[0]) || !isset($itemArray[1]) || !isset($itemArray[2])) {
                    $this->getLogger()->error("Detected malformed item in $hotbarName hotbar! Make sure that you use the format ID:META:COUNT.");
                    continue;
                }

                $item = ItemFactory::getInstance()->get((int)$itemArray[0], (int)$itemArray[1], (int)$itemArray[2]);
                $item->setCustomName($itemName);
                $item->setLore($itemData['Lore']);
                if($itemData['Enchant'])
                    $e = new Enchantment("", Rarity::MYTHIC, ItemFlags::ALL, ItemFlags::ALL, 1);
                    EnchantmentIdMap::getInstance()->register(-1, $e);
                    $item->addEnchantment(new EnchantmentInstance($e));
                    $items[$itemData['Slot']] = $item;
                $commands = $itemData['Commands'];
                $slot = $itemData['Slot'];
                $hotbarCommands[$slot] = $commands;
            }

	        if($items !== []) {
                $this->hotbars[$hotbarName] = HotbarFactory::make('command', $hotbarName, $items);
                foreach ($hotbarCommands ?? [] as $slot => $commands) {
                    /** @var CommandHotbar $hotbar */
                    $hotbar = $this->hotbars[$hotbarName];
                    $hotbar->setSlotCommands($slot, $commands);
                }
            } else
                $this->getLogger()->error('Detected empty hotbar! If you want to clear a players inventory please use the /hotbar clear command');
        }
    }
    public function registerHotbarWorlds(): void{
	    $server = $this->getServer();
	    $worldMngr = $server->getWorldManager();
	    foreach ($this->getConfig()->get('Worlds') as $levelName => $hotbarName) {
	        if($worldMngr->loadWorld($levelName) && ($level = $worldMngr->getWorldByName($levelName)) && $level !== null) {
                if(isset($this->hotbars[$hotbarName]))
                    $this->getHotbarLevels()->bindLevelToHotbar($level, $this->hotbars[$hotbarName]);
                else
                    $this->getLogger()->notice("Tried to bind hotbar $hotbarName to world but $levelName isn't defined!");
            } else
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
