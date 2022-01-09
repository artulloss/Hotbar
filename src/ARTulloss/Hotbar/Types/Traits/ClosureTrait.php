<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 7:17 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Types\Traits;

use pocketmine\player\Player;
use pocketmine\utils\Utils;
use Closure;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

trait ClosureTrait
{

    private Closure $closure;
    /**
     * @param Closure $closure
     */
    public function setClosure(Closure $closure): void{
        $this->closure = $closure;
    }
    /**
     * @return Closure|null
     */
    public function getClosure(): ?Closure{
        return $this->closure;
    }
    /**
     * @param Player $player
     * @param int $slot
     */
    public function executeClosure(Player $player, int $slot): void{
        $closure = $this->closure;
        Utils::validateCallableSignature(function (Player $player, int $slot): void{}, $closure);
        $closure($player, $slot);
    }
}