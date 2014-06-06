<?php

App::uses('AppHelper', 'Helper');

class JsSetupHelper extends AppHelper {

	public $helpers = array('Html');
	
	public function beforeRender($viewFile) {
		parent::beforeRender($viewFile);
		
		$this->Html->script('WebApp.web-app',        array('inline' => false, 'block' => 'layout-script'));
		$this->Html->script('WebApp.nprogress',      array('inline' => false, 'block' => 'layout-script'));
		$this->Html->script('WebApp.jquery.history', array('inline' => false, 'block' => 'layout-script'));
		$this->Html->css(   'WebApp.nprogress',      array('inline' => false, 'block' => 'layout-style'));
		$this->Html->css(   'WebApp.web-app',        array('inline' => false, 'block' => 'layout-style'));
	}
	
}