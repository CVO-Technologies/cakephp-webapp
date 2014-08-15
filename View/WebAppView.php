<?php

App::uses('JsonView', 'View');

class WebAppView extends JsonView {

	public $subDir = null;

	public function render($view = null, $layout = null) {
		$content = parent::render($view, $layout);
		parent::renderLayout($content, $layout);

		if ($layout === null) {
			$layout = $this->layout;
		}

		foreach ($this->blocks() as $cakeBlock) {
			$blocks[$cakeBlock] = $this->fetch($cakeBlock);
		}
		$blocks['content'] = $content;

		$title = __d('web_app', 'WebApp');
		if ($this->fetch('title')) {
			$title = $this->fetch('title');
		}
		if (isset($this->viewVars['title_for_page'])) {
			$title = $this->viewVars['title_for_page'];
		}

		$variables = array();
		$variables['title'] = $title;
		$variables['layout'] = $layout;

		$variables += $this->__getOriginalSerialised($this->get('_serialize', array()));

		$this->set(compact('blocks', 'variables'));
		$this->set('_serialize', array('blocks', 'variables'));

		return parent::render($view, $layout);
	}

	private function __getOriginalSerialised($serialize) {
		if (is_array($serialize)) {
			$data = array();
			foreach ($serialize as $alias => $key) {
				if (is_numeric($alias)) {
					$alias = $key;
				}
				if (array_key_exists($key, $this->viewVars)) {
					$data[$alias] = $this->viewVars[$key];
				}
			}
			$data = !empty($data) ? $data : null;
		} else {
			$data = isset($this->viewVars[$serialize]) ? $this->viewVars[$serialize] : null;
		}

		if (!is_array($data)) {
			return array();
		}

		return $data;
	}


}