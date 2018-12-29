<?php
use prodigyview\design\Observer;
use prodigyview\design\StaticObserver;

use PHPUnit\Framework\TestCase;

class TestInstance {
	
	use Observer;
	
	public function observe($string) {
		
		$this -> _notify(__FUNCTION__, $this, $string);
		
	}
}

class TestStatic {
	
	use Observer;
	
	public function observe($string) {
		
		self::_notify(__FUNCTION__, $this, $string);
		
	}
}

class InstanceObsever {
	
	public function observed($object, $string) {
		
	}
	
}

public function StaticObserver {
	
	
}
