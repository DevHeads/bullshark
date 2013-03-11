<?php
class map_mqueue extends map_everything {
	var $table  = 'mail_queue';
	var $entity = array
	(
		'id'=>'id',
		'to'=>'to',
		'subject'=>'subject',
		'text'=>'text'
	);
}