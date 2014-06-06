<?php

App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('WebAppView', 'WebApp.View');

class WebAppViewTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		//Configure::write('debug', 0);
	}

	public function testWebApp() {
		App::build(array(
			'View' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS)
		));
		$Request = new CakeRequest();
		$Response = new CakeResponse();
		$Controller = new Controller($Request, $Response);
		$Controller->name = $Controller->viewPath = 'Posts';

		$data = array(
			'User' => array(
				'username' => 'fake'
			),
			'Item' => array(
				array('name' => 'item1'),
				array('name' => 'item2')
			)
		);
		$Controller->set('user', $data);
		$View = new WebAppView($Controller);
		$output = $View->render('index');

		$this->assertJson($output);

		$json = json_decode($output, true);
		$this->assertSame('posts index', $json['blocks']['content']);
		$this->assertSame('application/json', $Response->type());
	}

}
