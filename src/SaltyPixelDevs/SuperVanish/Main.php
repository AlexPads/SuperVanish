<?php

declare(strict_types=1);

namespace SaltyPixelDevs\SuperVanish;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

class Main extends PluginBase implements Listener{

    public $pk;

    public function onEnable() : void{
    }


	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "sv":
				$sender->sendMessage("§aYou Are Entering Super Vanish!");
				$this->Vanish($sender);
				return true;
            case "esv":
                $sender->sendMessage("§cYou Are Leaving Super Vanish!");
                $this->UnVanish($sender);
				return true;
            case "fl":
                $this->fl($sender);
                return true;
			default:
				return false;
		}
	}
    public function Vanish(Player $player){
        if ($player->isOp()) {
            $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
            $player->setNameTagVisible(false);
            $player->sendPopup("§aYou are now Invisible to OtherPlayers!");
            $entry = new PlayerListEntry();
            $entry->uuid = $player->getUniqueId();

            $pk = new PlayerListPacket();
            $pk->entries[] = $entry;
            $pk->type = PlayerListPacket::TYPE_REMOVE;
            foreach ($this->getServer()->getOnlinePlayers() as $players) {
                $players->sendDataPacket($pk);
            }
        }else{
            $player->sendMessage("You Have to be OP to use this Command!");
        }
    }
    public function UnVanish(Player $player){
        if ($player->isOp()) {
            $player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
            $player->setNameTagVisible(true);
            $player = $player->getName();
            $this->getServer()->broadcastMessage("$player joined the game");
        }else{
            $player->sendMessage("You have to be OP to use this command!");
        }
    }
    public function fl(Player $player)
    {
        if ($player->isOp()) {
            $player->sendMessage("Sending Fake Leave Message!");
            $player = $player->getName();
            $this->getServer()->broadcastMessage("§e$player left the game");
        }else{
            $player->sendMessage("You have to be OP to use this command!");
        }
    }
}
