<?php
use prodigyview\design\Observer;
use prodigyview\design\StaticObserver;

use PHPUnit\Framework\TestCase;

class TestInstance {
	
	use Observer;
	
	public function observe($string, $unit) {
		
		$this -> _notify(__FUNCTION__, $this, $string, $unit);
		
	}
}

class TestStatic {
	
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
		
		$instance = new TestInstance();
		
		$instance -> addObserver('observe', 'TestInstance', function($object, $string, $unit) {
			
			$unit -> assertEquals('Pass Me Along', $string);
			
		}, array('type' => 'closure'));
		
		$instance->observe($string, $this);
		
	}
	
	public function testInstanceWithInstance() {
		
		$string = 'Pass Me Along To Instance';
		
		$instance = new TestInstance();
		
		$listener = new InstanceListener();
		
		$instance -> addObserver('observe', $listener , 'observed', array('type' => 'instance'));
		
		$instance->observe($string, $this);
		
	}
	
	public function testInstanceWithStatic() {
		
		$string = 'Pass Me Along To Static';
		
		$instance = new TestInstance();
		
		$instance -> setObserverTrace(true);
		
		$instance -> addObserver('observe', 'StaticListener' , 'observed', array('type' => 'static'));
		
		$instance->observe($string, $this);
	}

	public function testStaticWithClosure() {
		
		$string = 'Pass Me Along';
		
		TestStatic::addObserver('observe', 'TestStatic', function($object, $string, $unit) {
			
			$unit -> assertEquals('Pass Me Along', $string);
			
		}, array('type' => 'closure'));
		
		TestStatic::observe($string, $this);
		
	}
	
	public function testStaticWithInstance() {
		
		TestStatic::clearObservers('observe');
		
		$string = 'Pass Me Along To Instance';
		
		$listener = new InstanceListener();
		
		TestStatic::addObserver('observe', $listener , 'observed', array('type' => 'instance'));
		
		TestStatic::setObserverTrace(true);
		
		TestStatic::observe($string, $this);
		
	}
	
	public function testStaticWithStatic() {
		
		TestStatic::clearObservers('observe');
		
		$string = 'Pass Me Along To Static';
		
		TestStatic::addObserver('observe', 'StaticListener' , 'observed', array('type' => 'static'));
		
		TestStatic::observe($string, $this);
	}
	
}
