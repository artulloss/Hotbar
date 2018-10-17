<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 9/4/2018
 * Time: 8:55 PM
 */
declare(strict_types = 1);
namespace Hotbar;
use pocketmine\scheduler\Task;
use pocketmine\Server;
Class Cooldown extends Task
{
	protected $hotbar;
	private $player;

	public function __construct($hotbar, $player) {
		$this->hotbar = $hotbar;
		$this->player = $player;
	}

	public function onRun(int $currentTick) :void {
		echo "UNSET";
		$key = array_search($this->player, $this->hotbar->tap);
		unset($this->hotbar->tap[$key]);
		var_dump($this->hotbar->tap);
	}

}