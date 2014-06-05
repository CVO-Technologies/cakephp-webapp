<?php

class WebAppHelper extends AppHelper {
	
	public function afterRenderFile($viewFile, $content) {
		$blocks = $this->_View->blocks();
		//print_r($this->_View->viewVars);
		
		$data = array();
		
		foreach ($blocks as $block) {
			if ($block == 'content') {
		//		print_r($content);
				$data['blocks'][$block] = $content;
			} else {
				$data['blocks'][$block] = $this->_View->fetch($block);
			}
		}
		//print_r($data);
	
		$data['variables']['title'] = $this->_View->viewVars['title_for_layout'];
		
		return json_encode($data);
	}	
}