[![](https://poggit.pmmp.io/shield.state/Hotbar)](https://poggit.pmmp.io/p/Hotbar)
[![HitCount](http://hits.dwyl.io/artulloss/Hotbar.svg)](http://hits.dwyl.io/artulloss/Hotbar)
# Hotbar
A simple hotbar plugin! Add items and make them execute commands, on any world!
#### Configuration

The default configuration is pretty simple!
```
---
Items:
  world:
    §rDefault Config!:
      Item: "399:0:1"
      Slot: 0
      Lore:
      - §r§9This is the default configuration!
    #  - §rNext line
      Commands:
      - say This is the default configuration!@console
      
      # Replacable things!
      
      # {player} - In game name
      # {tag} - Nametag
      # {level} - Level
      # {x} - Player X
      # {y} - Player Y
      # {z} - Player Z
      
      # Normal Permissions!
      
      # @player
      # @here
      # @everyone
      
      # Run as OP
      
      # @PLAYER
      # @HERE
      # @EVERYONE
      
      Enchant: true
Locked Inventory:
- world
Cooldown: 0.5
...

```



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
