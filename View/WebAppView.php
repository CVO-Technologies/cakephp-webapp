<?php

App::uses('JsonView', 'View');

class WebAppView extends JsonView {

	public $subDir = null;

	public function __construct(Controller $controller = null) {
		parent::__construct($controller);

		Configure::write('debug', 0);
	}


	public function loadHelpers() {
		unset($this->viewVars['_serialize']);

		parent::loadHelpers();
	}

	public function render($view = null, $layout = null) {
		$content = parent::render($view, $layout);
		parent::renderLayout($content);

		foreach ($this->blocks() as $cakeBlock) {
			$blocks[$cakeBlock] = $this->fetch($cakeBlock);
		}
		$blocks['content'] = $content;

		$variables = array();
		$variables['title'] = (isset($this->viewVars['title_for_page'])) ? $this->viewVars['title_for_page'] : __('WebApp');

		$this->set(compact('blocks', 'variables'));
		$this->set('_serialize', array('blocks', 'variables'));

		return parent::render($view, $layout);
	}


}