<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/8/2019
 * Time: 9:18 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Events;

use ARTulloss\Hotbar\HotbarUser;
use pocketmine\event\Event;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class UseHotbarEvent extends Event
{
    /** @var HotbarUser $hotbarUser */
    private $hotbarUser;
    /** @var int $slot */
    private $slot;
    /**
     * UseHotbarEvent constructor.
     * @param HotbarUser $hotbarUser
     * @param int $slot
     */
    public function __construct(HotbarUser $hotbarUser, int $slot) {
        $this->hotbarUser = $hotbarUser;
        $this->slot = $slot;
    }
    /**
     * @return HotbarUser
     */
    public function getHotbarUser(): HotbarUser{
        return $this->hotbarUser;
    }
}