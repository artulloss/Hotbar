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
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\nbt\tag\ListTag;
class Hotbar extends PluginBase implements Listener {
    // world,slot,item,nameOfItem,command,playerOrConsole,glowing
    public $config = (array("Slots" => array("world,0,339:0,name,true,command,player"), "Cooldown" => 0.5)
    );
    private $tap;
    public $dataPath;
    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->dataPath = $this->getDataFolder() . 'hotbar.yml';
        if (!file_exists($this->dataPath)) {
            $resource = fopen($this->dataPath, 'w') or die("Unable to create hotbar.yml!");
            fwrite($resource, yaml_emit($this->config));
            $this->getLogger()->notice("Successfully created hotbar.yml");
        }
        $this->readConfig();
    }

    public function readConfig(): void {
        $this->config = yaml_parse_file($this->dataPath);
        unset($dataPath);
        $this->getLogger()->notice("Configuration read successfully");
    }

    public function onJoin(PlayerJoinEvent $event):void{

        $player = $event->getPlayer();
        $inv = $player->getInventory();

        foreach ($this->config["Slots"] as $slot){
            $explosion = explode(",", $slot);
            if($explosion[0] == $player->getLevel()->getName()){
                $bang = explode(":", $explosion[2]);
                $item = Item::get((int)$bang[0], (int)$bang[1], 1);
                $item->setCustomName($explosion[3]);
                if($explosion[4] == "true"){
                    $item->setNamedTagEntry(new ListTag("ench"));
                }
                $inv->setItem((int)$explosion[1], $item);
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event):void{

        $player = $event->getPlayer();
        $inv = $player->getInventory();

        foreach ($this->config["Slots"] as $slot){
            $explosion = explode(",", $slot);
            if($explosion[0] == $player->getLevel()->getName()){
                $bang = explode(":", $explosion[2]);
                $item = Item::get((int)$bang[0], (int)$bang[1], 1);
                $item->setCustomName($explosion[3]);
                if($explosion[4] == "true"){
                    $item->setNamedTagEntry(new ListTag("ench"));
                }
                $inv->setItem((int)$explosion[1], $item);
            }
        }
    }

    public function switchWorld(EntityLevelChangeEvent $event):void{
        $player = $event->getEntity();

        if(!$player instanceof Player) { return; }
        $inv = $player->getInventory();

        $player->getInventory()->clearAll();
        foreach ($this->config["Slots"] as $slot){
            $explosion = explode(",", $slot);
            if($explosion[0] == $event->getTarget()->getName()){
                $bang = explode(":", $explosion[2]);
                $item = Item::get((int)$bang[0], (int)$bang[1], 1);
                $item->setCustomName($explosion[3]);
                if($explosion[4] == "true"){
                    $item->setNamedTagEntry(new ListTag("ench"));
                }
                $inv->setItem((int)$explosion[1], $item);
            }
        }
    }


    public function onInteract(PlayerInteractEvent $event):void{
        $player = $event->getPlayer();
        if(!isset($this->tap[$player->getName()])){
            $this->tap[$player->getName()] = microtime(true);
            foreach ($this->config["Slots"] as $slot) {
                $explosion = explode(",", $slot);
                if($player->getInventory()->getItemInHand()->getId() == $explosion[2]){
                    if($explosion[6] == "console"){
                        $replaced = str_replace("{PLAYER}", $player->getName(), $explosion[5]);
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(),$replaced);
                        break;
                    }
                    if($explosion[6] == "player"){
                        $player->getServer()->dispatchCommand($player, $explosion[5]);
                        break;
                    }
                }
            }
        }
        if(microtime(true) - $this->tap[$player->getName()] <= $this->config["Cooldown"]){
            return; // Don't unset
        }
        unset($this->tap[$player->getName()]);
    }
}
