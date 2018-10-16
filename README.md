[![](https://poggit.pmmp.io/shield.state/Hotbar)](https://poggit.pmmp.io/p/Hotbar)
[![HitCount](http://hits.dwyl.io/artulloss/Hotbar.svg)](http://hits.dwyl.io/artulloss/Hotbar)
# Hotbar
A simple hotbar plugin! Add items and make them execute commands, on any world!
#### Configuration

The default configuration is pretty simple!
```
---
Items:
  world: # The default config uses the default world
    0:
      Item: "264:1:1"
      ItemName: Example 1
      Lore:
      - These examples will
      - make commands run
      - without OP
      Commands:
      - command@player
      - command@here
      - command@everyone
      Enchant: true
    8:
      Item: "264:2:1"
      ItemName: Example 2
      Lore:
      - These examples will
      - make commands run
      - with OP
      Commands:
      - command@PLAYER
      - command@HERE
      - command@EVERYONE
      - command@console
      - command@CONSOLE
      Enchant: false
Locked Inventory:
- world
Cooldown: 2
...

```


You can add as many items and worlds as you'd like by creating more entries with world names.

You can add as many commands as you'd like, and there are different @ types, indicating who will execute them (they're inspired by discord)

You can @ a player's IGN to make them execute something, provided they're online, however you can't make them execute it as OP, what if their name were all caps?

All caps letters means that the command will execute as OP, as explained by the default config


There are also some things you can use to get data about the player for commands, they are as follows:

{player} - In game name
{tag} - Nametag
{level} - Level
{x} - Player X
{y} - Player Y
{z} - Player Z


#### Duplicate items

You can deal with duplicate items by using names, lores or damage values!

Duplicate items are only an issue if on the same world!


#### Locking Inventories

You may wish to block a player from modifying the hotbar, the "Locked Inventory" part of the config allows for this:

```
Locked Inventory:
- example
- as  many as you want
- as many as you want
```

#### Cooldown
The cooldown is to "fix" the player interact spam, which is a client side bug with Windows 10, I recommend the default value, but you can set it as high/low as you'd like.

#### Support?
Tweet me [@artulloss](https://twitter.com/artulloss)
Discord [here](https://discord.versai.pro)
