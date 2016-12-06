<?php


namespace Fireworks;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddItemEntityPacket;
use pocketmine\item\Item;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\inventory\BigShapedRecipe;
use pocketmine\level\Position;
use pocketmine\level\particle\DustParticle;
use pocketmine\scheduler\AsyncTask;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\Server;
use pocketmine\utils\Utils;
use pocketmine\level\Explosion;
use pocketmine\level\Level;
use pocketmine\event\server\DataPacketReceiveEvent;

class Main extends PluginBase implements Listener{
	public $i = 0;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleAsyncTask(new CheckVersionTask($this));
		
  }
public function update(){
		    $this->getServer()->getScheduler()->scheduleTask(new UpdaterTask($this, $this->getDescription()->getVersion()));
	  }
	
		

	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		if($player->getInventory()->getItemInHand()->getId() === Item::STICK){
			$player->sendPopup("§eЕсли хочеш запустить фейрверк то возпользуйся красным факелом ID:76");
			}
		if($player->getInventory()->getItemInHand()->getId() === Item::LIT_REDSTONE_TORCH){
			$event->setCancelled(true);
			$player->getInventory()->removeItem(Item::get(76,0,1));
			$launchorigin = $event->getBlock()->getSide($event->getFace())->add(0.5, 0.5, 0.5);
			
			$pk = new AddItemEntityPacket();
			$eid = $pk->eid = Entity::$entityCount++;
			$pk->x = $launchorigin->x;
			$pk->y = $launchorigin->y;
			$pk->z = $launchorigin->z;
			$pk->speedX = 0;
			$pk->speedY = 1.5;
			$pk->speedZ = 0;
			$pk->item = Item::get(Item::LIT_REDSTONE_TORCH);
			$player->getLevel()->addChunkPacket($player->getX() >> 4, $player->getZ() >> 4, $pk);
			$block = $event->getBlock(); //получаем блок, по которому тапнули
			$x = $block->x; 
			$y = $block->y; //получаем координаты блока
			$z = $block->z; 
             $level = $player->getLevel();
             $center = new Vector3($x, $y, $z);
    
             $radius = 10.0;

             $count = 650;

             $r = mt_rand(0, 300);
             $g = mt_rand(0, 300);
             $b = mt_rand(0, 300);
             $center = new Vector3($x, $y + 3, $z); 
             $particle = new DustParticle($center, $r, $g, $b);
               for ($i = 0; $i < $count; $i++) {

               $pitch = (mt_rand() / mt_getrandmax() - 0.5) * M_PI;
               $yaw = mt_rand() / mt_getrandmax() * 2 * M_PI;
               $y = -sin($pitch);$delta = cos($pitch);
               $x = -sin($yaw) * $delta;
               $z = cos($yaw) * $delta;
               $v = new Vector3($x, $y + 5, $z);
               $p = $center->add($v->normalize()->multiply($radius));
               $particle->setComponents($p->x, $p->y, $p->z);
               $level->addParticle($particle);}
		}
	}
	
	public function addExplodeParticle(Position $pos, Particle $particle){
		$pos->getLevel()->addParticle($particle);
	}
}
