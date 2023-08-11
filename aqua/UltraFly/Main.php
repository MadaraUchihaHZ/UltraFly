<?php

namespace aqua\UltraFly;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {

    private $flyPlayers = [];

    public function onEnable() : void {
        $this->getLogger()->info("FlyPlugin has been enabled!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if ($command->getName() === "fly" && $sender instanceof Player) {
            if ($sender->hasPermission("UltraFly.cmd")) {
                $this->toggleFly($sender);
            } else {
                $sender->sendMessage(TF::RED . "You don't have permission to use this command.");
            }
        }
        return true;
    }

    public function onEntityDamage(EntityDamageEvent $event) : void {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                $this->disableFly($damager);
            }
            $entity = $event->getEntity();
            if ($entity instanceof Player) {
                $this->disableFly($entity);
            }
        }
    }

    public function toggleFly(Player $player) : void {
        if ($player->getAllowFlight()) {
            $this->disableFly($player);
        } else {
            $this->enableFly($player);
        }
    }

    public function enableFly(Player $player) : void {
        $player->setAllowFlight(true);
        $player->sendMessage(TF::GREEN . "Flight enabled!");
        $this->flyPlayers[$player->getName()] = true;
    }

    public function disableFly(Player $player) : void {
        if ($player->getAllowFlight()) {
            $player->setAllowFlight(false);
            $player->setFlying(false);
            $player->sendMessage(TF::RED . "Flight disabled!");
            unset($this->flyPlayers[$player->getName()]);
        }
    }
}
