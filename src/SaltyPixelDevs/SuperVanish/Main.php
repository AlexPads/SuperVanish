<?php

declare(strict_types=1);

namespace SaltyPixelDevs\SuperVanish;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use SaltyPixelDevs\SuperVanish\commands\sudo;
use SaltyPixelDevs\SuperVanish\Task\Join;

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
            case "sudo":
                $this->sudo($sender, $args);
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
            $name = $player->getName();
            $this->getServer()->broadcastMessage("§e$name left the game");
            $entry = new PlayerListEntry();
            $entry->uuid = $player->getUniqueId();

            $pk = new PlayerListPacket();
            $pk->entries[] = $entry;
            $pk->type = PlayerListPacket::TYPE_REMOVE;

            foreach($this->getServer()->getOnlinePlayers() as $players){
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
            $name = $player->getName();
            $this->getServer()->broadcastMessage("$name joined the game");
            $entry = new PlayerListEntry();
            $entry->uuid = $player->getUniqueId();
            $entry->entityUniqueId = $player->getId();
            $entry->xboxUserId = $player->getXuid();
            $entry->username = $player->getName();
            $entry->skin = $player->getSkin();
            $pk = new PlayerListPacket();
            $pk->entries[] = $entry;
            $pk->type = PlayerListPacket::TYPE_ADD;
            foreach($this->getServer()->getOnlinePlayers() as $players){

                $players->sendDataPacket($pk);
            }
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
    public function sudo(Player $player, $args){
        if ($player->isOp()) {
            if (isset($args[0]) && isset($args[1])) {
                $player = $this->getServer()->getPlayerExact($args[0]);
                if (strpos($args[1], ":") !== false) {
                    $list = explode(":", $args[1]);
                    if ($list[0] == "c") {
                        $player->sendMessage(TextFormat::GREEN . "Sending message as " . $player->getName());
                        $this->getServer()->getPluginManager()->callEvent($ev = new PlayerChatEvent($player, $list[1]));
                        if (!$ev->isCancelled()) {
                            $this->getServer()->broadcastMessage($this->getServer()->getLanguage()->translateString($ev->getFormat(), [$ev->getPlayer()->getDisplayName(), $ev->getMessage()]), $ev->getRecipients());
                        }
                    } else {
                        $player->sendMessage("Please use the command correctly!");
                    }
                } else {
                    $player->sendMessage(TextFormat::AQUA . "Command ran as " . $player->getName());
                    $this->getServer()->dispatchCommand($player, $args[1]);
                }
            }
        }
    }

}
