<?php
//ping,status,check,pause,whoareyou,joke,contact,plans,alpha2,router,localpeers,speedproblem,connectionproblem,torrent,offtopic,autologin,livetv,repost(search old posts),grievance,howinternetworks,dns,https,help

$time_start = microtime(true);
$pause=false;
$parent_id=file('parent_id.txt', FILE_IGNORE_NEW_LINES); //read parent ids of previous posts from file
$ch = curl_init();
$url='https://graph.facebook.com/v3.1/456642647737894/feed/?fields=message,created_time,comments.order(reverse_chronological).summary(1)&limit=5&since=-120%20seconds&access_token='.getenv("FB_PAGE_TOKEN");
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
			$reply="helpdesk@meghbelabroadband.com, 1800-102-5111 (tollfree), 033-4029-1100";
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
			$reply="Administrators of the facebook group, Pacenet Meghbela Broadband (PMPL) Forum, have built me to manage the group more effectively. I am not fully prepared yet; but I will be soon. I am not affiliated with PMPL or Meghbela Broadband. I am written in PHP and I periodically check for keyword every 60 seconds. I use Facebook graph API to interact with Facebook platform. Facebook requires all apps to get approved first. Once I get approved, I will be able to use webhook events and reply instantly. Please inform my creators if you have any suggestions. https://github.com/souravndp/PMPLbot-Our-Facebook-Group-Bot";
			post_reply($id, $reply);
		}
	}else if ((preg_match("/(?<=pmplbot\(check:).+(?=\))/i", $message, $match))){ //======================================================pmplbot(check:)
		echo '<u>Match found: ['.$id.']'.$message." match: ".$match[0]."</u></br>";
		if (!already_replied($id)){
			$reply=check_url(filter_var($match[0], FILTER_SANITIZE_URL));
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(status)') !== false){ //======================================================pmplbot(status)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="Running for: " . (microtime(true) - $GLOBALS('time_start'))." seconds";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(plans)') !== false){ //======================================================pmplbot(plans)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(alpha2)') !== false){ //======================================================pmplbot(alpha2)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(localpeers)') !== false){ //======================================================pmplbot(localpeers)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(livetv)') !== false){ //======================================================pmplbot(livetv)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(autologin)') !== false){ //======================================================pmplbot(autologin)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(dns)') !== false){ //======================================================pmplbot(dns)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(https)') !== false){ //======================================================pmplbot(https)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(offtopic)') !== false){ //======================================================pmplbot(offtopic)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(repost)') !== false){ //======================================================pmplbot(repost)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(torrent)') !== false){ //======================================================pmplbot(torrent)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(grievance)') !== false){ //======================================================pmplbot(grievance)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(speed') !== false){ //======================================================pmplbot(speedproblem)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(connection') !== false){ //======================================================pmplbot(connectionproblem)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(router)') !== false){ //======================================================pmplbot(router)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(howinternetworks)') !== false){ //======================================================pmplbot(howinternetworks)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(help)') !== false){ //======================================================pmplbot(help)
		echo '<u>Match found: ['.$id.']'.$message."</u></br>";
		if (!already_replied($id)){
			$reply="";
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
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://icanhazdadjoke.com/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT,10);
	$headers = array();
	$headers[] = "Accept: text/plain";
	$headers[] = "User-Agent: PMPLbot(https://github.com/souravndp/PMPLbot-Our-Facebook-Group-Bot)";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	echo 'Error:' . curl_error($ch);
	} else{
		switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		case 200:
			break;
		default:
			$result = "Unable to get Joke from the API";
		}
	}
	curl_close ($ch);
	return $result;
}
function check_url($url){
	if ( $parts = parse_url($url) ) {
		if ( !isset($parts["scheme"]) ){
		   $url = "http://$url";
		}
	}
	echo $url."<br>";
	if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
		$reply="The URL seems invalid";
	}else{
		$command='curl -L --connect-timeout 5 --max-time 8 -o /dev/null -sw "%{http_code}" "'.$url.'"';
		echo $command."<br>";
		$http_code=exec($command);
		echo $http_code."<br>";
		if(strpos($http_code,'000') === false){
			$reply="The URL seems UP/RECHABLE (HTTP CODE ".$http_code.") from my network. (Reverify at http://www.isitdownrightnow.com)";
		} else{
			$reply="The URL seems DOWN/UNRECHABLE from my network (Timeout set at 5 sec. Also reverify at http://www.isitdownrightnow.com)";
		}
	}
	return $reply;
}

//sleep for 5 seconds
//sleep(5);




















/*
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




if(!curl_errno($ch))
{
 $info = curl_getinfo($ch);

 echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
}

*/

//file_put_contents('temp.txt', file_get_contents('php://input').PHP_EOL, FILE_APPEND);
/*$headers =  getallheaders();
foreach($headers as $key=>$val){
  echo $key . ': ' . $val . '<br>';file_put_contents('temp.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
}*/
?>
