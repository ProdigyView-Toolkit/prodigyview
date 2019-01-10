<?php
use prodigyview\design\Observer;
use prodigyview\design\StaticObserver;

use PHPUnit\Framework\TestCase;

class ObserverTestInstance {
	
	use Observer;
	
	public function observe($string, $unit) {
		
		$this -> _notify(__FUNCTION__, $this, $string, $unit);
		
	}
}

class ObserverTestStatic {
	
	use StaticObserver;
	
	public static function observe($string, $unit) {
		
		self::_notify(__FUNCTION__, null, $string, $unit);
		
	}
}

class InstanceListener {
	
	public function observed($object, $string, $unit) {
		
		$unit -> assertEquals('Pass Me Along To Instance', $string);
		
	}
	
}

class StaticListener {
	
	public static function  observed($object, $string, $unit) {
		
		$unit -> assertEquals('Pass Me Along To Static', $string);
		
	}
}

class ObserverTests extends TestCase {

	public function testInstanceWithClosure() {
		
		$string = 'Pass Me Along';
		
		$instance = new ObserverTestInstance();
		
		$instance -> addObserver('observe', 'ObserverTestInstance', function($object, $string, $unit) {
			
			$unit -> assertEquals('Pass Me Along', $string);
			
		}, array('type' => 'closure'));
		
		$instance->observe($string, $this);
		
	}
	
	public function testInstanceWithInstance() {
		
		$string = 'Pass Me Along To Instance';
		
		$instance = new ObserverTestInstance();
		
		$listener = new InstanceListener();
		
		$instance -> addObserver('observe', $listener , 'observed', array('type' => 'instance'));
		
		$instance->observe($string, $this);
		
	}
	
	public function testInstanceWithStatic() {
		
		$string = 'Pass Me Along To Static';
		
		$instance = new ObserverTestInstance();
		
		$instance -> setObserverTrace(true);
		
		$instance -> addObserver('observe', 'StaticListener' , 'observed', array('type' => 'static'));
		
		$instance->observe($string, $this);
	}

	public function testStaticWithClosure() {
		
		$string = 'Pass Me Along';
		
		ObserverTestStatic::addObserver('observe', 'ObserverTestStatic', function($object, $string, $unit) {
			
			$unit -> assertEquals('Pass Me Along', $string);
			
		}, array('type' => 'closure'));
		
		ObserverTestStatic::observe($string, $this);
		
	}
	
	public function testStaticWithInstance() {
		
		ObserverTestStatic::clearObservers('observe');
		
		$string = 'Pass Me Along To Instance';
		
		$listener = new InstanceListener();
		
		ObserverTestStatic::addObserver('observe', $listener , 'observed', array('type' => 'instance'));
		
		ObserverTestStatic::setObserverTrace(true);
		
		ObserverTestStatic::observe($string, $this);
		
	}
	
	public function testStaticWithStatic() {
		
		ObserverTestStatic::clearObservers('observe');
		
		$string = 'Pass Me Along To Static';
		
		ObserverTestStatic::addObserver('observe', 'StaticListener' , 'observed', array('type' => 'static'));
		
		ObserverTestStatic::observe($string, $this);
	}
	
}
