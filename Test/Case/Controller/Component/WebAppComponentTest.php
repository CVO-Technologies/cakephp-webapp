<?php

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('WebAppComponent', 'WebApp.Controller/Component');

class TestWebAppComponentController extends Controller {

}

class WebAppComponentTest extends CakeTestCase {

	public $WebAppComponent = null;
	public $Controller = null;

	private $__debugLevel;

	public function setUp() {
		parent::setUp();

		$Collection = new ComponentCollection();
		$this->WebAppComponent = new WebAppComponent($Collection);

		$this->__debugLevel = Configure::read('debug');

		Configure::write('debug', 2);
	}

	public function testDetectorEnabled() {
		$CakeRequest = $this->_getRequest(true);
		$CakeResponse = new CakeResponse();

		$this->Controller = new TestWebAppComponentController($CakeRequest, $CakeResponse);
		$this->WebAppComponent->initialize($this->Controller);

		$this->assertTrue($CakeRequest->is('web-app'));
	}

	public function testDetectorDisabled() {
		$CakeRequest = $this->_getRequest(false);
		$CakeResponse = new CakeResponse();

		$this->Controller = new TestWebAppComponentController($CakeRequest, $CakeResponse);
		$this->WebAppComponent->initialize($this->Controller);

		$this->assertFalse($CakeRequest->is('web-app'));
	}

	public function testDebugDisabled() {
		$CakeRequest = $this->_getRequest(true);
		$CakeResponse = new CakeResponse();

		$this->Controller = new TestWebAppComponentController($CakeRequest, $CakeResponse);
		$this->WebAppComponent->initialize($this->Controller);

		$this->assertEquals(0, Configure::read('debug'));
	}

	public function testCorrectView() {
		$CakeRequest = $this->_getRequest(true);
		$CakeResponse = new CakeResponse();

		$this->Controller = new TestWebAppComponentController($CakeRequest, $CakeResponse);
		$this->WebAppComponent->initialize($this->Controller);

		$this->assertEquals('WebApp.WebApp', $this->Controller->viewClass);
	}

	public function testAjaxDisabled() {
		$CakeRequest = $this->_getRequest(true);
		$CakeResponse = new CakeResponse();

		$this->Controller = new TestWebAppComponentController($CakeRequest, $CakeResponse);
		$this->WebAppComponent->initialize($this->Controller);

		$this->assertFalse($CakeRequest->is('ajax'));
	}

	public function tearDown() {
		parent::tearDown();

		unset($this->WebAppComponent);
		unset($this->Controller);

		Configure::write('debug', $this->__debugLevel);
	}

	/**
	 * @param $enabled
	 * @return CakeRequest
	 */
	private function _getRequest($enabled) {
		$CakeRequest = $this->getMock('CakeRequest', array(
			'header'
		));
		$CakeRequest
			->staticExpects($this->any())
			->method('header')
			->with('X-Web-App')
			->will($this->returnValue(($enabled) ? 'true' : false));

		return $CakeRequest;
	}

}
