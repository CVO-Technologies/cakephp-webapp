<?php

class WebAppComponent extends Component {
	
	public $enabled = false;
	
	public $components = array('RequestHandler');
	
	public function startup(\Controller $controller) {
        if (isset($controller->request->query['web-app'])) {
            $this->enabled = (bool) $controller->request->query['web-app'];
        }
		
		if ($this->enabled) {
			$controller->helpers[] = 'WebApp.WebApp';
			
			Configure::write('debug', 0);
		}
	}
	
	public function beforeRender(\Controller $controller) {
		if ($this->enabled) {
			$controller->layout = false;
			
			$controller->response->type('json');
		}
	}
	
}