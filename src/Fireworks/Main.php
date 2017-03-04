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
use pocketmine\scheduler\PluginTask;
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
				$count = 650;
				$radius = 10;

				$task = new Task($this,$x,$y+20,$z,$level);
				$this->getServer()->getScheduler()->scheduleDelayedTask($task,20);

	}
}

	public function addExplodeParticle(Position $pos, Particle $particle){
		$pos->getLevel()->addParticle($particle);
	}
}


class Task extends PluginTask{
  public function __construct(PluginBase $owner,$x,$y,$z,$l){
    parent::__construct($owner);
	$this->x = $x;
	$this->y = $y;
	$this->z = $z;
	$this->l = $l;
  }
  public function onRun($tick){
	$radius = 4;

	$r = mt_rand(0, 300);
	$g = mt_rand(0, 300);
	$b = mt_rand(0, 300);
  		for($t = 0; $t <= 180; $t += 10){
    		$rad = deg2rad($t);
		$zt = $this->z+($radius * cos($rad));

			for($c = 0; $c < 360; $c += 10){
      				$rads = deg2rad($c);
				$center = new Vector3($this->x+($radius * sin($rad) * cos($rads)), $this->y+($radius * sin($rad) * sin($rads)), $zt);

        			$particle = new DustParticle($center, $r, $g, $b);
				$this->l->addParticle($particle);

		
	}

}


  }
}

