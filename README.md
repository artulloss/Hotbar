[![HitCount](http://hits.dwyl.io/artulloss/Hotbar.svg)](http://hits.dwyl.io/artulloss/Hotbar)
# Hotbar
A simple hotbar plugin!
#### Configuration

The configuration is pretty simple
```
---
Items:
  world:
  - Item: "399:0:1"
    ItemName: Name
    Command: Command
    Executor: Player
    Enchant: true
Locked Inventory: []
Cooldown: 0.500000
...
```

While most are pretty self explanatory, please note that Executor who is executing the command, either the player or the console

If you use console you can do {PLAYER} to refer to the player

You can add as many items and worlds as you'd like!
```
---
Items:
  FirstWorld:
  - Item: "399:0:1"
    ItemName: Name
    Command: Command
    Executor: Player
    Enchant: true
  SecondWorld:
  - Item: "339:0:1"
    ItemName: Name
    Command: Command
    Executor: Player
    Enchant: true
  - Item: "339:0:1"
    ItemName: Name
    Command: Command
    Executor: Player
    Enchant: true
Locked Inventory: []
Cooldown: 0.500000
...
```
#### Duplicate items

You can deal with duplicate items by using different meta values for them or different counts.

Duplicate items are only an issue if on the same world

In the example above, there isn't a conflict between 399:0:1 in the first world and the second world

However there is a conflict between the two 399:0:1's in the second world

Here is an example (The other parts are gone to save space)
```
SecondWorld:
  - Item: "339:0:1" # The first item
  - Item: "339:0:1" # The second one, this is invalid
  - Item: "339:1:1" # The second one, in a valid way
  - Item: "339:0:2" # The second one, also a valid way
  ```

**By Default items go in order of placement in config however you can change that! Remember, arrays start at 0, and so do item slots!**

```
---
Items:
  FirstWorld:
  - Item: '339:0:1'
    ItemName: Name
    Command: Command
    Executor: Player
    Enchant: true
  SecondWorld:
    '0':
      Item: '339:0:1'
      ItemName: Name
      Command: Command
      Executor: Player
      Enchant: true
    '8':
      Item: '339:1:1'
      ItemName: Name
      Command: Command
      Executor: Player
      Enchant: true
Locked Inventory: []
Cooldown: 0.5
```

#### Locking Inventories

You may wish to block a player from modifying the hotbar, the "Locked Inventory" part of the config allows for this:

```
Locked Inventory:
- example
- as many as you want
```

#### Cooldown
The cooldown is to "fix" the player interact spam, which is a client side bug with Windows 10, I recommend the default value, but you can set it as high/low as you'd like.

#### Support?
Tweet me [@artulloss](https://twitter.com/artulloss)
Discord [here](https://discord.versai.pro)
