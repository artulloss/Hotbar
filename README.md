[![](https://poggit.pmmp.io/shield.state/Hotbar)](https://poggit.pmmp.io/p/Hotbar)
[![HitCount](http://hits.dwyl.io/artulloss/Hotbar.svg)](http://hits.dwyl.io/artulloss/Hotbar)
# Hotbar
A simple hotbar plugin! Add items and make them execute commands, on any world!
## Configuration

The [default configuration](https://github.com/artulloss/Hotbar/blob/master/resources/config.yml) provides lots of help setting up the plugin!

#### Command format

For every item you need to add commands in order to do things.
This plugin accepts an [array](http://php.net/manual/en/book.array.php) of commands, allowing you to execute multiple commands with one tap!
The format of each command is, the command to be executed@executor@false, the first part is obviously the command, but the second and third
parts are a little more complicated.

#### Executors in commands

Executors are as follows, note that most are capitilization insensitive.

Player - The player who tapped the item
Here - The players looking at the player who tapped the item
Everyone - Everyone online on your server
Console - The servers console
Player's name, for example ARTulloss (my ign) (Players names ARE capitilization sensitive)

The third part of the command formate specifies if you want to have the player run the command as an OP, or have them run it without OP

#### Replacable in commands
You can use these in your commands to make refrences to the player!
These are capitilization sensitive, so make sure to type them as shown here!
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

For every name that needs to be the same, insert a §r at the end of the items name.
This will make them visually equivalent, but unique and not conflicting.

Duplicate item names are only an issue if on the same world!

#### Duplicate items

You can deal with duplicate items by using different names, lores or damage values!
You can deal with duplicate items by using different lores or damage values!

Duplicate items are only an issue if on the same world!


#### Locking Inventories

You may wish to block a player from modifying the hotbar, the "Locked Inventory" part of the config allows for this:

```
Locked Inventory:
- example
- world
```

#### Cooldown
The cooldown is to "fix" the player interact spam, which is a client side bug with Windows 10, I recommend the default value, but you can set it as high/low as you'd like.

#### Support?
Tweet me [@artulloss](https://twitter.com/artulloss)
Discord [here](https://discord.versai.pro)
