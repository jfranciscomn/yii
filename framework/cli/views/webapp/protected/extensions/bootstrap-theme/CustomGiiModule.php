<?php 

Yii::import('system.gii.GiiModule');
Yii::import('system.gii.CCodeGenerator');
Yii::import('system.gii.CCodeModel');
Yii::import('system.gii.CCodeFile');
Yii::import('system.gii.CCodeForm');


class CustomGiiModule extends GiiModule {
	public $Custom= array(
		'StringKey'=>'name',
	);
}
?>
