<?php

if (CakePlugin::loaded('Croogo')) {
	Croogo::hookComponent('*', 'WebApp.WebApp');

	Croogo::hookHelper('*', 'WebApp.JsSetup');
}
