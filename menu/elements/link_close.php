<?php defined('isCMS') or die;
	
	// WRAPPER CLOSE
	
	if (isset($module -> var[$type]['wrapper'])) {
		$module -> var[$type]['wrapper'] -> close();
	}
	
	// LI/A CLOSE
	
	if (isset($module -> var[$type]['li'])) {
		$module -> var[$type]['li'] -> close();
	}
	
?>