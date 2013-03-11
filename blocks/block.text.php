<?php
/**
 * Projekt: BullShark
 * Author: Kirill S. Holodilin
 * Date: 2010, March
 * Block: text, simple
 */

class blockText
{
    var $version='blockText v.0.1';
    public function run($state,$arg)
    {
       return 'This is '.$arg.' text block called with arguments: '.$arg.' at time: '.time().'<br />';
    }
}
?>