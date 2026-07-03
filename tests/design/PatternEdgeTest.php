<?php

use PHPUnit\Framework\TestCase;
use prodigyview\design\Adapter;
use prodigyview\design\Filter;
use prodigyview\design\Observer;

class PatternEdgeAdapterSubject {
	use Adapter;

	public function combine($value, $nullable = null, array $payload = array()) {
		if ($this->_hasAdapter(self::class, __FUNCTION__)) {
			return $this->_callAdapter(self::class, __FUNCTION__, $value, $nullable, $payload);
		}

		return array($value, $nullable, $payload);
	}
}

class PatternEdgeFilterSubject {
	use Filter;

	public function apply($data, $event = null) {
		return $this->_applyFilter(self::class, __FUNCTION__, $data, array('event' => $event));
	}
}

class PatternEdgeObserverSubject {
	use Observer;

	public function fire($payload) {
		$this->_notify('edge', $this, $payload);
	}

	public function fireMissingEvent() {
		$this->_notify('missing', $this);
	}
}

class PatternEdgeTest extends TestCase {

	public function testAdapterReceivesNullsAndArraysWithoutLosingShape() {
		$subject = new PatternEdgeAdapterSubject();

		$subject->addAdapter(PatternEdgeAdapterSubject::class, 'combine', function($value, $nullable, array $payload) {
			return array(
				'value' => $value,
				'nullable' => $nullable,
				'payload' => $payload,
			);
		}, array('type' => 'closure'));

		$this->assertSame(array(
			'value' => 'edge',
			'nullable' => null,
			'payload' => array('nested' => array('a' => 1)),
		), $subject->combine('edge', null, array('nested' => array('a' => 1))));
	}

	public function testRemoveClassAdapterRestoresOriginalBehavior() {
		$subject = new PatternEdgeAdapterSubject();

		$subject->addAdapter(PatternEdgeAdapterSubject::class, 'combine', function() {
			return 'adapted';
		}, array('type' => 'closure'));

		$this->assertSame('adapted', $subject->combine('edge'));

		$subject->removeClassAdapter(PatternEdgeAdapterSubject::class);

		$this->assertSame(array('edge', null, array()), $subject->combine('edge'));
	}

	public function testFiltersOnlyRunForMatchingEventAndRemainChainable() {
		$subject = new PatternEdgeFilterSubject();

		$subject->addFilter(PatternEdgeFilterSubject::class, 'apply', 'closure', function($data) {
			$data[] = 'before';
			return $data;
		}, array('type' => 'closure', 'event' => 'before'));

		$subject->addFilter(PatternEdgeFilterSubject::class, 'apply', 'closure', function($data) {
			$data[] = 'after';
			return $data;
		}, array('type' => 'closure', 'event' => 'after'));

		$this->assertSame(array('start'), $subject->apply(array('start')));
		$this->assertSame(array('start', 'before'), $subject->apply(array('start'), 'before'));
		$this->assertSame(array('start', 'after'), $subject->apply(array('start'), 'after'));
	}

	public function testObserversCanBeClearedAndMissingEventsAreNoOps() {
		$subject = new PatternEdgeObserverSubject();
		$count = 0;

		$subject->fireMissingEvent();

		$subject->addObserver('edge', 'closure', function($object, $payload) use (&$count) {
			$count += $payload['amount'];
		}, array('type' => 'closure'));

		$subject->fire(array('amount' => 2));
		$subject->clearObservers('edge');
		$subject->fire(array('amount' => 2));

		$this->assertSame(2, $count);
	}
}
