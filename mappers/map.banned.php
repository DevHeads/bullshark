<?php
class map_banned extends map_everything {
	var $table  = 'banned';
	var $entity = array
	(
		'id'=>'id',
		'ip'=>'ip',
        'user'=>'user',
        'level'=>'level',
		'status'=>'status',
		'time'=>'time',
        'reason'=>'reason'
	);
}