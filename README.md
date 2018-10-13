[![HitCount](http://hits.dwyl.io/artulloss/Hotbar.svg)](http://hits.dwyl.io/artulloss/Hotbar)
# Hotbar
A simple hotbar plugin!
#### Configuration

The default configuration is pretty simple
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

#### Duplicate items

You can deal with duplicate items by using different meta values for them or different counts.

Duplicate items are only an issue if on the same world

In the example above, there isn't a conflict between 399:0:1 in the first world and the second world

However there is a conflict between the two 399:0:1's in the second world

I recommend starting at meta 1 for most items, as that way the same item but not in the hotbar won't trigger the items command

Here is an example (The other parts are gone to save space)
```
SecondWorld:
  - Item: "339:0:1" # The first item
  - Item: "339:0:1" # The second one, this is invalid
  - Item: "339:1:1" # The second one, in a valid way
  - Item: "339:0:2" # The second one, also a valid way
```


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
