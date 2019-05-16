[![](https://poggit.pmmp.io/shield.state/Hotbar)](https://poggit.pmmp.io/p/Hotbar) [![](https://poggit.pmmp.io/shield.api/Hotbar)](https://poggit.pmmp.io/p/Hotbar)
# Hotbar
A versatile and full featured hotbar plugin! Add items and make them execute commands, on any world!
## Configuration

The [default configuration](https://github.com/artulloss/Hotbar/blob/master/resources/config.yml) provides lots of help setting up the plugin!

#### Hotbars

Hotbars are inventory containers for the slots 1-9, you can define them in the config.yml file, and make them automatically appear in worlds using the Worlds part of the configuration.

```yaml
Worlds: # Here you can specify hotbars above and pair them with worlds
  world: UniqueHotbarName
 ```

If you don't have them automatically appear, you can still call them by calling the command `hotbar {hotbarName} {player}`

#### Command format

For every item you need to add commands in order to do things.
This plugin accepts multiple commands per tap, and they will be executed in the order that they're listed in.

Commands are listed in the format of commandToExecute@executor where executor must be either player, op or console.

You can also leave out the @executor part for most commands by specifying a default way for them to be run using the Default-Command-Options part of the config.
```yaml
Default-Command-Options: player # or op or console
```

#### Replaceable in commands
You can use these in your commands to make references to the player!
These are capitalization sensitive, so make sure to type them as shown here!
```
{player}  IGN of the player
{tag}     Name tag of the player
{level}   The players level

{x}       The players X
{y}       The players Y
{z}       The players Z
```

#### Locking Inventories

You may wish to block a player from modifying their inventory when they have a hotbar, the "Locked Inventory" part of the config allows for this:

```yaml
Locked Inventory:
- example
- world
```

Locked inventories are unable to have items moved in them, and players with locked inventories can't pickup items or arrows.

*Note that items will be locked until the Hotbar is removed from the player.*

#### The Hotbar Command

Previously, the hotbar command could only be used by the console, however now there is "hotbar" permission that allows a user to access the command.

There are a few uses of the hotbar command.

To remove a hotbar simply type the command
```
hotbar {clear} {player}
```

where player is the players name. The brackets around clear are to make sure you don't name a world that by mistake, in which case it will clear rather than send the hotbar. This will also unlock the players inventory and allow them to move items.

To send a hotbar to a player you can use the command like so

```
hotbar {hotbarName} {player}
```

Where {hotbarName} represents the name of the hotbar and {player} is the players name (leave out the brackets here)

You are also able to list all hotbars using the list argument:

```
hotbar {list}
```

#### Cooldown
The cooldown is to "fix" the player interact spam bug, which is a client side issue on Windows 10, I recommend the default value, but you can set it as high/low as you'd like.

#### API

This plugin comes with a full API and allows for the creation of Hotbars that use closures to execute code as well as hotbars that execute commands. To make use of this a plugin would simply need to create a new ClosureHotbar or CommandHotbar and then do 
```php
// Make sure $player is an instance of the Player class
$player->getServer()->getPluginManager()->getPlugin('Hotbar')->getHotbarUsers()->assign($player, $hotbar);
```

#### Demo of this plugin
To see this plugin in action, you can join my server: versai.pro 19132

#### Support?
Tweet me [@artulloss](https://twitter.com/artulloss)
Discord [here](https://discord.versai.pro)
