<?php
//read parent ids of previous posts from file
$parent_id=file('parent_id.txt', FILE_IGNORE_NEW_LINES);
//print_r($parent_id);
$ch = curl_init();
$url='https://graph.facebook.com/v3.1/456642647737894/feed/?fields=message,created_time,comments.order(reverse_chronological).summary(1)&limit=5&since=-300%20seconds&access_token='.getenv("FB_PAGE_TOKEN");
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$feeds = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else echo date('Y/m/d H:i:s').'<hr>';
curl_close ($ch);
$obj_feeds = json_decode($feeds, true);
foreach($obj_feeds['data'] as $post){
	//echo '<u><b>['.$post['id'].']['.$post['created_time'].']'.$post['message'].'</b></u><br>';
	match_keyword($post['message'],$post['id']);
	foreach($post['comments']['data'] as $comment){
		//echo '['.$comment['id'].']['.$comment['created_time'].']'.$comment['message'].'<br>';
		match_keyword($comment['id'],$comment['message']);
	}
}

function match_keyword($id, $message){
	global $parent_id; //inviting the global variable
	if (strpos($message,'pmplbot(ping)') !== false) { //======================================================pmplbot(ping)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="pong";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(contact)') !== false) { //======================================================pmplbot(contact)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="helpdesk@meghbelabroadband.com";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(joke)') !== false){ //======================================================pmplbot(joke)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply=get_joke();
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(whoareyou)') !== false){ //======================================================pmplbot(whoareyou)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="Administrators of the facebook group, Pacenet Meghbela Broadband (PMPL) Forum, have built me to manage the group more effectively. I am not fully prepared yet; but I will be soon. I am not affiliated with PMPL or Meghbela Broadband. https://github.com/souravndp/PMPLbot-Our-Facebook-Group-Bot";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot') !== false){ //======================================================pmplbot( xxx )
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="unknown keyword";
			post_reply($id, $reply);
		}
	}
}

function already_replied($id){
	global $parent_id;
	if (in_array($id, $parent_id)){
		echo "Already replied ".$id."</br>";
		return true;
	} else{
		return false;
	}
}
function post_reply($id, $reply){
	global $parent_id; //inviting the global variable
	$url='https://graph.facebook.com/v3.1/'.$id.'/comments?message='.urlencode($reply).'&access_token='.getenv("FB_PAGE_TOKEN");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	$reply_id = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}else{
		echo $reply_id."</br>";
		array_push($parent_id, $id);
		file_put_contents('parent_id.txt', $id.PHP_EOL, FILE_APPEND);
	}
	curl_close ($ch);
}
function get_joke(){
	$opts = [
		"http" => [
			"method" => "GET",
			"header" => "Accept: text/plain"
		]
	];
	$context = stream_context_create($opts);
	return file_get_contents('https://icanhazdadjoke.com/', false, $context);
}
























//file_put_contents('temp.txt', file_get_contents('php://input').PHP_EOL, FILE_APPEND);
/*$headers =  getallheaders();
foreach($headers as $key=>$val){
  echo $key . ': ' . $val . '<br>';file_put_contents('temp.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
}*/
?>
