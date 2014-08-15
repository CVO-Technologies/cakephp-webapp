<?php

class WebAppComponent extends Component {

	public $enabled = false;

	public $components = array('RequestHandler');

	public function initialize(Controller $controller) {
		$controller->request->addDetector(
			'web-app',
			array(
				'callback' => function (CakeRequest $request) {
					return $request->header('X-Web-App') === 'true' && !isset($request->params['ext']);
				}
			)
		);

		$controller->response->header('X-Web-App', ($controller->request->is('web-app')) ? 'true' : 'false');

		if ($controller->request->is('web-app')) {
			$controller->viewClass = 'WebApp.WebApp';

			$controller->request->addDetector(
				'ajax',
				array(
					'callback' => function ($request) {
						return false;
					}
				)
			);

			Configure::write('debug', 0);
		}
	}

}