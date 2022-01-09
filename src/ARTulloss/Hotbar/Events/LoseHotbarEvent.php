<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 5/18/2019
 * Time: 3:45 PM
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

class LoseHotbarEvent extends Event
{
    private HotbarUser $hotbarUser;

    /**
     * LoseHotbarEvent constructor.
     * @param HotbarUser $hotbarUser
     */
    public function __construct(HotbarUser $hotbarUser) {
        $this->hotbarUser = $hotbarUser;
    }
    /**
     * @return HotbarUser
     */
    public function getHotbarUser(): HotbarUser{
        return $this->hotbarUser;
    }
}