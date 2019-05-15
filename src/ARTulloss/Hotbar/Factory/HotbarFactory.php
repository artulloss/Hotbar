<?php
/**
 * Created by PhpStorm.
 * User: Adam
 * Date: 4/4/2019
 * Time: 7:35 PM
 */
declare(strict_types=1);

namespace ARTulloss\Hotbar\Factory;

use ARTulloss\Hotbar\Types\ClosureHotbar;
use ARTulloss\Hotbar\Types\CommandHotbar;
use ARTulloss\Hotbar\Types\Hotbar;
use TypeError;
use function strtolower;

/**
 *  _  _  __ _____ __  __  ___
 * | || |/__\_   _|  \/  \| _ \
 * | >< | \/ || | | -< /\ | v /
 * |_||_|\__/ |_| |__/_||_|_|_\
 *
 * @author ARTulloss
 * @link https://github.com/artulloss
 */

class HotbarFactory
{
    /**
     * @param string $type
     * @param array $items
     * @return Hotbar
     */
    static public function make(string $type, array $items): Hotbar{
        switch (strtolower($type)) {
            case 'command':
                return new CommandHotbar($items);
            case 'closure':
                return new ClosureHotbar($items);
            default:
                throw new TypeError('Unknown hotbar type');
        }
    }
}