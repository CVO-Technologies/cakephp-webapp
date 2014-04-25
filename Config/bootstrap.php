<?php

App::uses('CakeEventListener', 'Event');
App::uses('CakeEventManager', 'Event');

class WebAppScriptListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
			'Controller.beforeRender' => 'beforeLayout'
        );
    }

    public function beforeLayout(CakeEvent $event) {
		$controller = $event->subject();
		
		$controller->helpers[] = 'WebApp.JsSetup';
    }
}

// Attach the UserStatistic object to the Order's event manager
$listener = new WebAppScriptListener();
CakeEventManager::instance()->attach($listener);