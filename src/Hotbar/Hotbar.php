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
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use const pocketmine\IS_DEVELOPMENT_BUILD;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\TextFormat;

class Hotbar extends PluginBase implements Listener {
    public $config;
    private $tap;
    public $dataPath;
    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->dataPath = $this->getDataFolder() . 'hotbar.yml';
        $world = $this->getServer()->getDefaultLevel()->getName();
        if (!file_exists($this->dataPath)) {
            $this->config = array (
                'Items' =>
                    array (
                        $world =>
                            array (
                                0 =>
                                    array (
                                        'Item' => '264:1:1',
                                        'ItemName' => 'Example 1',
                                        'Lore' =>
                                            array (
                                                0 => 'These examples will',
                                                1 => 'make commands run',
                                                2 => 'without OP',
                                            ),
                                        'Commands' =>
                                            array (
                                                0 => 'command@player',
                                                1 => 'command@here',
                                                2 => 'command@everyone',
                                            ),
                                        'Enchant' => true,
                                    ),
                                8 =>
                                    array (
                                        'Item' => '264:2:1',
                                        'ItemName' => 'Example 2',
                                        'Lore' =>
                                            array (
                                                0 => 'These examples will',
                                                1 => 'make commands run',
                                                2 => 'with OP',
                                            ),
                                        'Commands' =>
                                            array (
                                                0 => 'command@PLAYER',
                                                1 => 'command@HERE',
                                                2 => 'command@EVERYONE',
                                                3 => 'command@console',
                                                4 => 'command@CONSOLE',
                                            ),
                                        'Enchant' => false,
                                    ),
                            ),
                    ),
                'Locked Inventory' =>
                    array (
                        0 => $world,
                    ),
                'Cooldown' => 2
            );
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
                    $item->setLore($slotData['Lore']);
                    $player->getInventory()->setItem($slot, $item);
                }
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event) :void {

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
                    $item->setLore($slotData['Lore']);
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
                    $item->setLore($slotData['Lore']);
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
                        $hand = $player->getInventory()->getItemInHand();

                        // ID, DATA, NAME, LORE

                        if ($hand->getId() == $bang[0] && $hand->getDamage() == $bang[1] && $hand->getName() == $slotData['ItemName'] && $hand->getLore() == $slotData['Lore']) {
                            foreach($slotData['Commands'] as $commandData){
                                $data = explode("@", $commandData);
                                $replace = array(
                                    "{player}", // IGN
                                    "{tag}", // Name tag
                                    "{level}", // Player level
                                    "{x}", // Player X
                                    "{y}", // PLayer Y
                                    "{z}", // Player Z
                                );
                                $replaceWith = array(
                                    $player->getName(), // IGN
                                    $player->getNameTag(), // Name tag
                                    $player->getLevel()->getName(), // Player level
                                    $player->getX(), // Player X
                                    $player->getY(), // PLayer Y
                                    $player->getZ(), // Player Z
                                );
                                $command = str_replace($replace, $replaceWith, $data[0]);
                                switch ($data[1]){
                                    case "player":
                                        $player->getServer()->dispatchCommand($player, $command);
                                        break;
                                    case "PLAYER":
                                        if($player->isOp()){
                                            $player->getServer()->dispatchCommand($player, $command);
                                        } else {
                                            $player->setOp(true);
                                            $player->getServer()->dispatchCommand($player, $command);
                                            $player->setOp(false);
                                        }
                                        break;
                                    case "everyone":
                                        foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
                                            $player->getServer()->dispatchCommand($onlinePlayer, $command);
                                        }
                                        break;
                                    case "EVERYONE":
                                        foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer){
                                            if($onlinePlayer->isOp()){
                                                $onlinePlayer->getServer()->dispatchCommand($onlinePlayer, $command);
                                            } else {
                                                $onlinePlayer->setOp(true);
                                                $onlinePlayer->getServer()->dispatchCommand($player, $command);
                                                $onlinePlayer->setOp(false);
                                            }
                                        }
                                        break;
                                    case "here":
                                        foreach ($player->getViewers() as $viewer){
                                            if($viewer->isOp()){
                                                $viewer->getServer()->dispatchCommand($onlinePlayer, $command);
                                            } else {
                                                $viewer->setOp(true);
                                                $viewer->getServer()->dispatchCommand($player, $command);
                                                $viewer->setOp(false);
                                            }
                                        }
                                        break;
                                    case "console":
                                    case "CONSOLE":
                                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $command);
                                        break;
                                    default:
                                        foreach ($this->getServer()->getOnlinePlayers() as $p){
                                            if($p->getName() == $data[1]){
                                                $player->getServer()->dispatchCommand($p, $command);
                                            }
                                        }
                                }
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
