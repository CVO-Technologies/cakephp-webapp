<?php

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('WebAppView', 'WebApp.View');

class WebAppViewTest extends CakeTestCase {

	public $Response;
	public $Controller = null;

	public function setUp() {
		parent::setUp();

		$Request = new CakeRequest();
		$this->Response = new CakeResponse();
		$this->Controller = new Controller($Request, $this->Response);
		$this->Controller->name = $this->Controller->viewPath = 'Posts';

		App::build(array(
			'View' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS)
		));
	}

	public function testViewLoad() {
		$this->Controller->viewClass = 'WebApp.WebApp';

		$this->Controller->render('index');

		$this->assertInstanceOf('WebAppView', $this->Controller->View);
	}

	public function testJsonResponse() {
		$View = new WebAppView($this->Controller);
		$output = $View->render('index');

		$this->assertJson($output);
		$this->assertSame('application/json', $this->Response->type());
	}

	public function testJsonStructure() {
		$View = new WebAppView($this->Controller);

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertArrayHasKey('blocks', $json);
		$this->assertInternalType('array', $json['blocks']);

		$this->assertArrayHasKey('variables', $json);
		$this->assertInternalType('array', $json['variables']);

		$this->assertArrayHasKey('title', $json['variables']);
		$this->assertInternalType('string', $json['variables']['title']);
	}

	public function testTitleBlock() {
		$View = new WebAppView($this->Controller);
		$View->assign('title', 'Title!');

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertSame('Title!', $json['variables']['title']);
	}

	public function testTitleForLayout() {
		$this->Controller->set('title_for_page', 'Title!');

		$View = new WebAppView($this->Controller);

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertSame('Title!', $json['variables']['title']);
	}

	public function testBlockAndTitleForLayout() {
		$this->Controller->set('title_for_page', 'Title for layout!');

		$View = new WebAppView($this->Controller);

		$View->assign('title', 'Title block!');

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertSame('Title for layout!', $json['variables']['title']);
	}

	public function testContent() {
		$View = new WebAppView($this->Controller);

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertSame('posts index', $json['blocks']['content']);
	}

	public function testLayout() {
		$this->Controller->layout = 'ajax';
		$View = new WebAppView($this->Controller);

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertSame('posts index', $json['blocks']['content']);
	}

	public function testSerializedExtras() {
		$this->Controller->set('key', 'value');
		$this->Controller->set('_serialize', array('key'));

		$View = new WebAppView($this->Controller);

		$output = $View->render('index');
		$json = json_decode($output, true);

		$this->assertSame('value', $json['variables']['key']);
	}

}
