<?php
//ping,status,check:,redflag,whoareyou,joke,contact,plans,alpha2,router,localpeers,speedproblem,connectionproblem,torrent,offtopic,autologin,livetv,repost(search old posts),grievance,howinternetworks,dns,https,help,onlinerecharge, wifiproblem, movierequest, conduct, regulations, portforward, liveip, showoff, suggest/bug, lco

ignore_user_abort(true);
set_time_limit(0);
ob_implicit_flush(true); //flush()
ob_end_clean(); //ob_flush()
pcntl_signal(SIGINT,  "sig_handler");
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
$hLock=fopen(__FILE__.".lock", "w+");
if(!flock($hLock, LOCK_EX | LOCK_NB))
	die("Already running. Exiting...");
logger("========================================STARTING SCRIPT========================================");
$time_start = microtime(true);
$redflag=false;
$loopcount=1;
$version='0.0.1';
if(is_file('parent_id.txt'))
	$parent_id=file('parent_id.txt', FILE_IGNORE_NEW_LINES); //read parent ids of previous posts from file
while (!file_exists('stop.txt')) {
	logger("--------------------starting loop(".$loopcount.")--------------------");
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	$ch = curl_init();
	$url='https://graph.facebook.com/v3.1/456642647737894/feed/?fields=message,created_time,comments.order(reverse_chronological).summary(1)&limit=5&since=-120%20seconds&access_token='.getenv("FB_PAGE_TOKEN");
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT,10);
	$feeds = curl_exec($ch);
	if (curl_errno($ch)) {
		logger( 'Error while fetching feeds:' . curl_error($ch));
	} else{
		switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		case 200:
			logger('Successfylly fetched feeds');
			$obj_feeds = json_decode($feeds, true);
			pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
			foreach($obj_feeds['data'] as $post){
				//echo '<u><b>['.$post['id'].']['.$post['created_time'].']'.$post['message'].'</b></u><br>';
				match_keyword($post['message'],$post['id']);
				foreach($post['comments']['data'] as $comment){
					//echo '['.$comment['id'].']['.$comment['created_time'].']'.$comment['message'].'<br>';
					match_keyword($comment['id'],$comment['message']);
				}
			}
			break;
		default:
			logger( 'Error while fetching feeds. HTTP CODE:' . $http_code);
		}
	}
	curl_close ($ch);
	$loopcount++;
	logger("sleeping");
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	sleep(30);
}
//=============================================================================
function match_keyword($id, $message){
	global $parent_id; //inviting the global variable
	if (strpos($message,'pmplbot(ping)') !== false) { //--------------------pmplbot(ping)--------------------
		if (!already_replied($id,$message)){
			$reply="pong";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(contact)') !== false) { //--------------------pmplbot(contact)--------------------
		if (!already_replied($id,$message)){
			$reply="helpdesk@meghbelabroadband.com, 1800-102-5111 (tollfree), 033-4029-1100
			More: pmplbot(grievance)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(joke)') !== false){ //--------------------pmplbot(joke)--------------------
		if (!already_replied($id,$message)){
			$reply=get_joke();
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(whoareyou)') !== false){ //--------------------pmplbot(whoareyou)--------------------
		if (!already_replied($id,$message)){
			$reply="Administrators of the facebook group, Pacenet Meghbela Broadband (PMPL) Forum, have built me to manage the group more effectively. I am not fully prepared yet; but I will be soon. I am not affiliated with PMPL or Meghbela Broadband.
			I am written in PHP and I periodically check for keyword every 60 seconds. I use Facebook graph API to interact with Facebook platform.
			Facebook requires all apps to get approved first. Once I get approved, I will be able to use webhook events and reply instantly.
			Please inform my creators if you have any suggestions. https://github.com/souravndp/PMPLbot-Our-Facebook-Group-Bot";
			post_reply($id, $reply);
		}
	}else if ((preg_match("/(?<=pmplbot\(check:).+(?=\))/i", $message, $match))){ //--------------------pmplbot(check:)--------------------
		if (!already_replied($id,$message)){
			$reply=check_url(filter_var($match[0], FILTER_SANITIZE_URL));
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(status)') !== false){ //--------------------pmplbot(status)--------------------
		if (!already_replied($id,$message)){
			$reply="Running for: " . (microtime(true) - $GLOBALS['time_start'])." seconds and loopcount:".$GLOBALS['loopcount']."
			Beta version".$GLOBALS['version'].".
			==CAUTION==If I misbehave, please use the keyword pmplbot(redflag). It will stop me from posting any comment for the current cycle until restarted. This flag is only for serious incidents (eg: I am posting random comments very frequently etc.). If you use it without proper reason, my human friends will permanently ban/block you from the group.";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(plans)') !== false){ //--------------------pmplbot(plans)--------------------
		if (!already_replied($id,$message)){
			$reply="Meghbela Broadband has different plans depending on the location. It is not possible to make a full list of all available plans but the following plans are available at most locations. (+18% tax is applicable)
			Alpha (30 Mbps, 31 Days, Rs 500)
			Beta (45 Mbps, 31 Days, Rs 650)
			Gamma (60 Mbps, 31 Days, Rs 800)
			Delta (75 Mbps, 31 Days, Rs 1250)
			Omega (100 Mbps, 31 Days, Rs 2200)
			Visit meghbelabroadband.com for full plan lists. If your lcoal plan is not available on the website, please contact the ISP for explanation. Verify plan list from the ISP before recharging. The above information is provided as is and I have no responsibiliy for the accuracy.";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(alpha2)') !== false){ //--------------------pmplbot(alpha2)--------------------
		if (!already_replied($id,$message)){
			$reply="alpha2 is a plan which offers 60 Mbps bandwidth and it costs Rs 500 + 18% GST. alpha2 is not yet officially launched. Please ask your LCO for more details. If your LCO denies the existence of such plan, please see these old group posts.
			https://www.facebook.com/groups/pmplusers/permalink/1931689790233165/
			https://www.facebook.com/groups/pmplusers/permalink/1971467219588755/";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(localpeers)') !== false){ //--------------------pmplbot(localpeers)--------------------
		if (!already_replied($id,$message)){
			$reply="Thank you for your interest in localpeers. the website URL is https://localpeers.com You need to register first using social login. localpeers also has an android app (https://localpeers.com/localpeers-android-app/). Localpeers works on any ISP network. Know more: https://www.facebook.com/groups/pmplusers/permalink/1558006504268164/";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(livetv)') !== false){ //--------------------pmplbot(livetv)--------------------
		if (!already_replied($id,$message)){
			$reply="http://thoptv.org/, https://localpeers.com/local-tv/, or you can donate to localpeers and access local-tv VIP cloud (https://www.facebook.com/groups/pmplusers/permalink/1978165188918958/) Live TV works on any ISP network. ";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(autologin)') !== false){ //--------------------pmplbot(autologin)--------------------
		if (!already_replied($id,$message)){
			$reply="You need to send an email to helpdesk@meghbelabroadband.com Don't forget to mention your username and the ISP helpdesk executive will activate autologin feature free of cost. Remember that autologin (without MAC binding) can reduce your account security and you should never share your IP address with anyone";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(dns)') !== false){ //--------------------pmplbot(dns)--------------------
		if (!already_replied($id,$message)){
			$reply="Meghbela Boradband intercepts and redirects all DNS requests to their own server. So, It doesn't matter which DNS you use. We requested the ISP to allow users to use public DNS servers and they denied the request with the following comment: 'For security reason the organization can’t provide open DNS facility.'
			How can you tell if your ISP is redirecting your DNS queries? (https://www.facebook.com/groups/pmplusers/learning_content/?filter=1272969312845177&post=1933431940058950)
			Why DNS redirection is bad for you (https://www.facebook.com/groups/pmplusers/learning_content/?filter=1272969312845177&post=1933440606724750)
			How to use the DNS server of your own choice and protect your browsing history from the ISP (https://www.facebook.com/groups/pmplusers/permalink/1939092786159532/)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(https)') !== false){ //--------------------pmplbot(https)--------------------
		if (!already_replied($id,$message)){
			$reply="Some users reported that Meghbela discriminates traffic based on protocol.(https://www.facebook.com/groups/pmplusers/learning_content/?filter=1272969312845177&post=1933426356726175) Thus they are getting plan speed on HTTP contents but not getting proper bandwidth on HTTPS or FTP or while using VPN.
			Meghbela Broadband denied this alligation with the following comment: 'Organization provide bandwidth as per the package policy and the difference of throughput as claimed is due to the established handshaking between the peer network elements where this organization is having no control.'
			However, some users still get low bandwidth on HTTPS websites like github, netflix etc. Unfortunately if you call helpline, they will download ubuntu etc. (read cached/CDN/peered cotents) and claim that their network is okay. If you can show enough evidence, somertimes, only if you are lucky, they take a docket and resolve it temporarily. For this purpose, you can use the following list of HTTPS and FTP linux mirrors: (https://www.facebook.com/groups/pmplusers/learning_content/?filter=1272969312845177&post=1801185129950299)
			If you think that your internet experience is getting degraded or your consumer rights are getting violated because of this, then please take some of your time and report these incidents to TRAI/pgportal with video evidence.
			Use the following keyworks for more info pmplbot(grievance), pmplbot(regulations)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(offtopic)') !== false){ //--------------------pmplbot(offtopic)--------------------
		if (!already_replied($id,$message)){
			$reply="According to the above commenter, this post does not fit within the allowed topic list for this group. offtopic posts should have #OT or #offtopic hashtag at the beginning. We have a very broad definition of items which are considered ontopic. We generally allow moderate offtopic posts but if the topic is blantly offtopic then we will delete it. Please note that self promotion and spam is strictly prohibited. Repeated violatations will result in removal of the OP form this group. I have notified the human moderators. Their decision will be considered final.";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(repost)') !== false){ //--------------------pmplbot(repost)--------------------
		if (!already_replied($id,$message)){
			$reply="According to the above commenter, this topic has already been discussed in this group. Please search the group for similar posts.";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(torrent)') !== false){ //--------------------pmplbot(torrent)--------------------
		if (!already_replied($id,$message)){
			$reply="If you are not getting peering speed on torrent, then please contact helpdesk. The users here can't help you with that. Torrent is completely legal in India if you are not downloading any copyrighted material. Currently Meghbela don't give exta bandwidth (torrent peering bandwidth) for torrent downloads. The torrent bandwidth is same as the plan bandwidth.
			Some Useful Popular Torrent Sites: https://www.facebook.com/groups/pmplusers/permalink/1815982765137202/
			How to create and share torrent(https://www.facebook.com/groups/pmplusers/permalink/1393770567358426/)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(grievance)') !== false){ //--------------------pmplbot(grievance)--------------------
		if (!already_replied($id,$message)){
			$reply="This are some emails of Meghbela Broadbsn managenment or the advisory group. nodal@meghbelabroadband.com (nodal officer), deepalaya_wb_ngo@yahoo.co.in (NGO, advisory group), subhankar.dutta@meghbelabroadband.com (services-head), rehan@meghbelabroadband.com (Senior Manager - Regulatory). Read more at: https://www.facebook.com/groups/pmplusers/learning_content/?filter=1667488453378902
			Please use the above emails only if your grievance is not resolved within the QoS time limit by sending an email to the helpdesk. The above information is provided as is, the infromation can be outdated and I have no responsibiliy for the accuracy. If you still have some more time to fight for your consumer rights, my human friends have some emails of TRAI/Detarptment of Telecommunications. Please contact the admins";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(connection') !== false){ //--------------------pmplbot(sconnectionproblem)--------------------
		if (!already_replied($id,$message)){
			$reply="Thank you for sharing your connection problem with us. This helps other user to get an overall picture of the quality of the network and the users can help you if they have suffered the similar situation before. You should also contact the helpdesk of the ISP if you think the problem is not arising from your own equipment(s). more: pmplbot(contact)
			For troubleshooting, check ping at your local gateway, 172.17.8.1 and 8.8.8.8(eg: open cmd and run: ping 172.17.8.1 -t) to see if any RTO(request timed out) is occuring.
			Try connecting the cable directly to your computer, rather than connecting through a personal router (this step is required to eleminate any probelm arising from your router). more: pmplbot(wifiproblem)
			If you have already notified the ISP but you are still facing the same problem continuously after 3-4 days, you may need to escalate the grievance to the higer level. more: pmplbot(grievance)
			If your line is not restored within 3 days, then the ISP need to refund 7 days charge or extend validity for 7 days. Similarly 15 days if the connection if the restoration takes more 7 days and 30 dyas in case the issue remanins unresolved for more than 15 days. This is your consumer right but remember to take sufficient proof, so that you can prove this later, in case the ISP denies to refund/extend and you need to approach pgportal/consumer affairs. more: pmplbot(grievance), pmplbot(regulations)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(speed') !== false){ //--------------------pmplbot(speedproblem)--------------------
		if (!already_replied($id,$message)){
			$reply="Thank you for sharing your speed problem with us. The users here can't do much to improve the situation but this helps other user to get an overall picture of the quality of the network. You should contact the helpdesk of the ISP. more: pmplbot(contact)
			For troubleshooting, check ping(open cmd and run: ping 172.17.8.1 -t) to see if any RTO(request timed out) is occuring, If you are getting low speed on a perticular website, then measure speed upto ISP node (Go to speedtest.net and select 'Meghbela Cable & Broadband Services Pvt. Ltd' from the server list) and check if the bandwidth upto ISP node is okay. If you get low speed upto ISP node, then register a docket at helpdesk and this type of problem is fixed soon if your local LCO is helpful (in case this is a local problem). If you get low speed on a particular website, then the issue is unlikely to be resolved soon, especially if the website is not popular. However you can still try to convince the helpdesk to register a docket. But as other users have reported earlier, they will probably download some cached/peered/CDN contents and claim everything is okay. more: pmplbot(grievance), pmplbot(https)
			Try connecting the cable directly to your computer, rather than connecting through a personal router (this step is required to eleminate any probelm arising from your router)
			Please try to include the following screenshots while posting about bandwidth problem.
			1. Speed upto ISP node (Go to speedtest.net and select 'Meghbela Cable & Broadband Services Pvt. Ltd' from the server list)
			2. Speed test after selecting some other servers preferably some server outside India
			3. Ping at your local gateway
			4. Ping at 172.17.8.1 (ping 172.17.8.1 -t)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(router)') !== false){ //--------------------pmplbot(router)--------------------
		if (!already_replied($id,$message)){
			$reply="Rouer suggestions depend on the other user's own perspective and experiance. The suggestions are mostly biased. Users have done similar discussions in the past. Please search the group for those posts.
			First you need to fix a budget (eg: less than 1000, around 3000, more than 5000 etc). Otherwise, you will never get perfect suggestion.
			Then the rule of thumb is always go for gigabit routers and dual band routers if your budget permits. Here is a word of caution, the advertised speed is generally wireless speed. Suppose a router mentions 300Mbps, that means the wireless speed is 300Mbps. If you want to use 200Mbps conenction from your ISP, this router won't work and you need to have a router with gigabit WAN port. WAN port speed is mentioned separately in the specification. Low end routers have generally 10/100 port which is not gigabit.";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(howinternetworks)') !== false){ //------------------pmplbot(howinternetworks)------------------
		if (!already_replied($id,$message)){
			$reply="Understanding the basics of the internet will help us to use this marvelous technology more efficiently and will also make us more confident. Learn more through interesting videos here: https://www.facebook.com/groups/pmplusers/learning_content/?filter=1067618223407494";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot(help)') !== false){ //--------------------pmplbot(help)--------------------
		if (!already_replied($id,$message)){
			$reply="The following keywords are available
			(under construction)";
			post_reply($id, $reply);
		}
	}else if (strpos($message,'pmplbot') !== false){ //--------------------pmplbot( xxx )--------------------
		if (!already_replied($id,$message)){
			$reply="unknown keyword. more: pmplbot(help)";
			post_reply($id, $reply);
		}
	}
}
//=============================================================================
function already_replied($id,$message){
	global $parent_id;
	if (in_array($id, $parent_id)){
		//logger("Already replied ".$id);
		return true;
	} else{
		logger("Found MATCH to reply [".$id."]".$message);
		return false;
	}
}
//=============================================================================
function post_reply($id, $reply){
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	global $parent_id; //inviting the global variable
	if($GLOBALS['redflag']){
		logger("Found redflag true. returning without posting comment");
		return;
	}
	$url='https://graph.facebook.com/v3.1/'.$id.'/comments?message='.urlencode($reply).'&access_token='.getenv("FB_PAGE_TOKEN");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT,5);
	$reply_id = curl_exec($ch);
	if (curl_errno($ch)) {
		logger( 'Error while posting comment:' . curl_error($ch));
	}else{
		switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		case 200:
			if (strpos($reply_id,'"id":') !== false){
				logger( "Successfully posted comment. Received reply:" . $reply_id);
				array_push($parent_id, $id);
				file_put_contents('parent_id.txt', $id.PHP_EOL, FILE_APPEND);
				file_put_contents('comment_id.txt', $reply_id.PHP_EOL, FILE_APPEND);
			}else{
				logger( "Error while posting comment. Didn't receive the comment success id. Received reply:" . $reply_id);
			}
			break;
		default:
			logger( 'Error while posting comment. HTTP CODE:' . $http_code);
		}
	}
	curl_close ($ch);
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
}
//=============================================================================
function get_joke(){
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://icanhazdadjoke.com/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	//curl_setopt($ch, CURLOPT_HEADER, true); //removed because it adds header information in the reply result. will curl_getinfo work now?
	curl_setopt($ch, CURLOPT_TIMEOUT,10);
	$headers = array();
	$headers[] = "Accept: text/plain";
	$headers[] = "User-Agent: PMPLbot(https://github.com/souravndp/PMPLbot-Our-Facebook-Group-Bot)";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		logger( 'Error while getting joke:' . curl_error($ch));
	} else{
		switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		case 200:
			logger("Got Joke from the API");
			break;
		default:
			$result = "Unable to get Joke from the API";
			logger("Unable to get Joke from the API. HTTP CODE:" . $http_code);
		}
	}
	curl_close ($ch);
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	logger($result);
	return $result;
}
//=============================================================================
function check_url($url){
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	if ( $parts = parse_url($url) ) {
		if ( !isset($parts["scheme"]) ){
		   $url = "http://$url";
		}
	}
	if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
		$reply="The URL seems invalid";
		logger($reply."URL: ".url);
	}else{
		$command='curl -L --connect-timeout 5 --max-time 8 -o /dev/null -sw "%{http_code}" "'.$url.'"'; //in windows use nul or c:\nul in the place of /dev/null
		logger($command);
		$http_code=exec($command);
		if(strpos($http_code,'000') === false){
			$reply="The URL seems UP/RECHABLE (HTTP CODE ".$http_code.") from my network. (Reverify at http://www.isitdownrightnow.com)";
			logger($reply);
		} else{
			$reply="The URL seems DOWN/UNRECHABLE from my network (Timeout set at 5 sec. Also reverify at http://www.isitdownrightnow.com)";
			logger($reply."HTTP CODE: ".$http_code);
		}
	}
	pcntl_signal_dispatch(); //DISPATCHING QUEUED SIGNALS
	return $reply;
}
//=============================================================================
function logger($message){
	$message="[".date('Y/m/d H:i:s')."] ".$message;
	echo $message."</br>";
	file_put_contents('fblog.txt', $message.PHP_EOL , FILE_APPEND);
	//https://gist.github.com/troy/2220679
	$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	$syslog_message = "<22>" . date('M d H:i:s ') . 'PMPLBOT: ' . $message;
	socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, 'logs6.papertrailapp.com', '52066'); //TODO: save these into env
	socket_close($sock);
}
//=============================================================================
function sig_handler($sig) {
    flock($hLock, LOCK_UN);
	fclose($hLock);
	unlink(__FILE__.".lock");
	logger("========================================RECEIVED ".$sig." EXITING========================================");
	exit();
}
//=============================================================================
flock($hLock, LOCK_UN);
fclose($hLock);
unlink(__FILE__.".lock");
logger("========================================STOPPING SCRIPT========================================");


















/*

	$opts = [
		"http" => [
			"method" => "GET",
			"header" => "Accept: text/plain"
		]
	];
	$context = stream_context_create($opts);
	echo file_get_contents('https://icanhazdadjoke.com/', false, $context);
}

===============================================================

if(!curl_errno($ch))
{
 $info = curl_getinfo($ch);

 echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
}

===============================================================

$data = file_get_contents('filename.txt');
while (!file_exists('stop.txt')) {
    // Add 1 to $data
    $data = $data+1;
    // Update file
    file_put_contents('filename.txt', $data);

    // Wait 4 seconds
    sleep(4);
}

===============================================================
curl --max-time 60 -L -o "C:/users/sourav/desktop/temp.file" -sw "%{size_download}" http://speedtest.tele2.net/10GB.zip
http://speedtest-blr1.digitalocean.com/100mb.test
curl --max-time 10 -o nul -sw "%{speed_download}" http://speedtest.tele2.net/10GB.zip
BUT NUL DOWNLOAD SPEED IS TOO SLOW. NO IDEA WHY

===============================================================


*/

//file_put_contents('temp.txt', file_get_contents('php://input').PHP_EOL, FILE_APPEND);
/*$headers =  getallheaders();
foreach($headers as $key=>$val){
  echo $key . ': ' . $val . '<br>';file_put_contents('temp.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
}*/
?>
