<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 10/12/2018
 * Time: 9:13 AM
 */
declare(strict_types = 1);
namespace Hotbar;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\nbt\tag\ListTag;
class Hotbar extends PluginBase implements Listener {
    public $config;
    private $tap;
    public $dataPath;
    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->dataPath = $this->getDataFolder() . 'hotbar.yml';
        $world = $this->getServer()->getDefaultLevel()->getName();
        if (!file_exists($this->dataPath)) {
            // The default config will create nether stars, one in the 0 slot and one in the 8 slot in the default level, to generate a config that shows how to do them in any order you want
            $this->config = array ( 'Items' => array ( $world => array ( 0 => array ( 'Item' => '399:1:1', 'ItemName' => 'Name', 'Command' => 'Command', 'Executor' => 'Player', 'Enchant' => true, ), 8 => array ( 'Item' => '399:9:1', 'ItemName' => 'Name', 'Command' => 'Command', 'Executor' => 'Player', 'Enchant' => true, ), ), ), 'Locked Inventory' => array ( 0 => $world, ), 'Cooldown' => 0.5 );
            $resource = fopen($this->dataPath, 'w') or die("Unable to create hotbar.yml!");
            fwrite($resource, yaml_emit($this->config));
            $this->getLogger()->notice("Successfully created hotbar.yml");
            fclose($resource);
        }
        $this->readConfig();
    }

    public function readConfig(): void {
        $this->config = yaml_parse_file($this->dataPath);
        unset($dataPath);
        $this->getLogger()->notice("Configuration read successfully");
        var_export($this->config);
    }

    public function onJoin(PlayerJoinEvent $event) :void {

        $player = $event->getPlayer();

        $player->getInventory()->clearAll();

        foreach ($this->config['Items'] as $key => $world){
            if($key == $player->getLevel()->getName()){
                foreach ($world as $slot => $slotData){
                    $bang = explode(":", $slotData['Item']);
                    $item = Item::get((int)$bang[0], (int)$bang[1], (int)$bang[2]);
                    $item->setCustomName($slotData['ItemName']);
                    if($slotData['Enchant']){
                        $item->setNamedTagEntry(new ListTag("ench"));
                    }
                    $player->getInventory()->setItem($slot, $item);
                }
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event) :void {

        $player = $event->getPlayer();

        foreach ($this->config['Items'] as $key => $world){
            if($key == $player->getLevel()->getName()){
                foreach ($world as $slot => $slotData){
                    $bang = explode(":", $slotData['Item']);
                    $item = Item::get((int)$bang[0], (int)$bang[1], (int)$bang[2]);
                    $item->setCustomName($slotData['ItemName']);
                    if($slotData['Enchant']){
                        $item->setNamedTagEntry(new ListTag("ench"));
                    }
                    $player->getInventory()->setItem($slot, $item);
                }
            }
        }
    }

    public function moveInventory(InventoryTransactionEvent $event) :void {

        $player = $event->getTransaction()->getSource();

        if(in_array($player->getLevel()->getName(), $this->config['Locked Inventory'])){
            $event->setCancelled();
        }
    }

    public function switchWorld(EntityLevelChangeEvent $event) :void {

        $player = $event->getEntity();

        if(!$player instanceof Player) { return; }

        $player->getInventory()->clearAll();

        foreach ($this->config['Items'] as $key => $world){
            if($key == $event->getTarget()->getName()){
                foreach ($world as $slot => $slotData){
                    $bang = explode(":", $slotData['Item']);
                    $item = Item::get((int)$bang[0], (int)$bang[1], (int)$bang[2]);
                    $item->setCustomName($slotData['ItemName']);
                    if($slotData['Enchant']){
                        $item->setNamedTagEntry(new ListTag("ench"));
                    }
                    $player->getInventory()->setItem($slot, $item);
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event) :void {
        $player = $event->getPlayer();
        if (!isset($this->tap[$player->getName()])) {
            $this->tap[$player->getName()] = $this->config['Cooldown'];
            foreach ($this->config['Items'] as $key => $world) {
                if ($key == $player->getLevel()->getName()) {
                    foreach ($world as $slot => $slotData) {
                        $bang = explode(":", $slotData['Item']);
                        if ($player->getInventory()->getItemInHand()->getId() == $bang[0] && $player->getInventory()->getItemInHand()->getDamage() == $bang[1] && $player->getInventory()->getItemInHand()->getCount() == $bang[2]) {
                            if (strtolower($slotData['Executor']) == "player") {
                                $player->getServer()->dispatchCommand($player, $slotData['Command']);
                                $this->tap[$player->getName()] = microtime(true);
                                break;
                            }
                            if (strtolower($slotData['Executor']) == "console") {
                                $replaced = str_replace("{PLAYER}", $player->getName(), $slotData['Command']);
                                $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $replaced);
                            }
                            $this->tap[$player->getName()] = microtime(true);
                            break;
                        }
                    }
                    break;
                }
            }
        }

        if(isset($this->tap[$player->getName()])){
            if(microtime(true) - $this->tap[$player->getName()] <= $this->config["Cooldown"]){
                return; // Don't unset
            }
        }
        unset($this->tap[$player->getName()]);
    }
}
