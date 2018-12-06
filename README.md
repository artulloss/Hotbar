[![](https://poggit.pmmp.io/shield.state/Hotbar)](https://poggit.pmmp.io/p/Hotbar)
# Hotbar
A versatile and full featured hotbar plugin! Add items and make them execute commands, on any world!
## Configuration

The [default configuration](https://github.com/artulloss/Hotbar/blob/master/resources/config.yml) provides lots of help setting up the plugin!

#### Types of Hotbars

There are two types of hotbars.

The first type is the Worlds hotbars, they automatically are given to you when you enter a world or respawn and go to that world.

The second type is the secondary-hotbars, they aren't given to you automatically, but are able to be called using the hotbar command from console.

The configurations for both types of hotbars are identical.

To call a secondary hotbar, have an item execute
      - hotbar (The name of the hotbar goes here) {player}@console@false
as one of it's commands.

To call the Worlds hotbar, have an item execute
      - hotbar WORLD {player}@console@false
as one of it's commands.

To avoid conflicting issues do not use WORLD as the name of a world or as a hotbar's name.

This system of multiple types of hotbars allow you to create complex systems, but I would keep it simple to avoid confusion from players.

The hotbars are also callable from other plugins using the hotbar command.

#### Command format

For every item you need to add commands in order to do things.
This plugin accepts an [array](http://php.net/manual/en/book.array.php) of commands, allowing you to execute multiple commands with one tap!
The format of each command is, the command to be executed@executor@false, the first part is obviously the command, but the second and third
parts are a little more complicated.

#### Executors in commands

Executors are as follows, note that most are capitalization insensitive.

Player - The player who tapped the item
Here - The players looking at the player who tapped the item
Everyone - Everyone online on your server
Console - The servers console
Player's name, for example ARTulloss (my ign) (Players names ARE capitilization sensitive)

The third part of the command format specifies if you want to have the player run the command as an OP, or have them run it without OP. I recommend leaving this false in most cases unless absolutely needed. If console is executing, ensure it's false because there is no reason at all for OP.

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
    
{rx}      The players X (Rounded)
{ry}      The players Y (Rounded)
{rz}      The players Z (Rounded)
```
#### Duplicate item names

For every name that needs to be the same, insert a color reset at the end of the items name.
This will make them visually equivalent, but unique and not conflicting.

Duplicate item names are only an issue if on the same world!

#### Duplicate items

You can deal with duplicate items by using different names, lores or damage values!

Duplicate items are only an issue if on the same world or secondary hotbar!


#### Locking Inventories

You may wish to block a player from modifying the hotbar, the "Locked Inventory" part of the config allows for this:

```
Locked Inventory:
- example
- world
```

#### Cooldown
The cooldown is to "fix" the player interact spam, which is a client side bug with Windows 10, I recommend the default value, but you can set it as high/low as you'd like.

#### Demo of this plugin
To see this plugin in action, you can join my server: versai.pro 19132

#### Support?
Tweet me [@artulloss](https://twitter.com/artulloss)
Discord [here](https://discord.versai.pro)
