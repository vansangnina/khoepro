<?php 
	include "config.php";
	$id = $_POST['id'];
	$upload = $_POST['upload'];
	$action = $_POST['action'];
	$table = $_POST['table'];
	$item = $d->rawQueryOne("select * from #_".$table." where id=?",array($id));
	if(unlink('../../upload/'.$upload.'/'.$item[$action])){
		$data[$action] = '';
		$d->where('id', $id);
	    $d->update($table, $data);

		$photoDetail = array();
	    $photoDetail['upload'] = '../../upload/'.$upload.'/';
	    $photoDetail['image'] = '';
	    $photoDetail['dimension'] = "Width: " . $config['product'][$type]['width'] . " px - Height: " . $config['product'][$type]['height'] . " px (" . $config['product'][$type]['img_type'] . ")";

	    echo $func->getImage(['class' => 'rounded', 'size-error' => '250x250x1', 'upload' => $photoDetail['upload'], 'image' => $photoDetail['image'], 'alt' => 'Alt Photo']);
	}
?>