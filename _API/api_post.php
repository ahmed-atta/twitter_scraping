<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_error_handler("var_dump");

require('config.php');
//define('DS', DIRECTORY_SEPARATOR);
//define('ROOT',dirname(__FILE__));


//============================================= //

function remove_emoji($tweet_txt){

	$tweet_txt = str_replace('<img alt="📩" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f4e9.svg" class="css-9pa8cd">' , "", $tweet_txt);
	$tweet_txt = str_replace('<img alt="🖋" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f58b.svg" class="css-9pa8cd">',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="🇸🇦" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f1f8-1f1e6.svg" class="css-9pa8cd">',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="🔴" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f534.svg" class="css-9pa8cd">',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="💌" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f48c.svg" class="css-9pa8cd">',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="📨" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f4e8.svg" class="css-9pa8cd">',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="📨" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f4e8.svg" class="css-9pa8cd"><br>',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="🏡" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f3e1.svg" class="css-9pa8cd">',"", $tweet_txt);
	$tweet_txt = str_replace('<img alt="🏫" draggable="false" src="https://abs-0.twimg.com/emoji/v2/svg/1f3eb.svg" class="css-9pa8cd">',"", $tweet_txt);
	
	

	$tweet_txt = str_replace('<img class="Emoji Emoji--forText" src="https://abs.twimg.com/emoji/v2/72x72/2708.png" draggable="false" alt="✈️" title="Airplane" aria-label="Emoji: Airplane">',"", $tweet_txt);
			
			
	$tweet_txt  = str_replace('رسالة مـﻧـــ الخاصــ', "", $tweet_txt );
	$tweet_txt  = str_replace('﴿ #مكة ﴾', "", $tweet_txt );
	$tweet_txt  = str_replace('مـﻧـــ الخاصــــ', "", $tweet_txt);
	$tweet_txt  = str_replace('مـﻧـــ الخاصــ', "", $tweet_txt );
	$tweet_txt  = str_replace('من الخاص', "", $tweet_txt );
	$tweet_txt  = str_replace('سؤال وردني على الخاص', "", $tweet_txt );
	$tweet_txt  = str_replace('<img class="Emoji Emoji--forText" src="https://abs.twimg.com/emoji/v2/72x72/2049.png" draggable="false" alt="⁉️" title="Exclamation question mark" aria-label="Emoji: Exclamation question mark">', "", $tweet_txt );
		 
	$tweet_txt  = str_replace('يقول :', "", $tweet_txt );
	$tweet_txt = str_replace('مِـﻧَـــ  اٌلِـخٓاٌصــــ',"",$tweet_txt);
	$tweet_txt  = str_replace('متابعينك', "", $tweet_txt );
	$tweet_txt  = str_replace('المتابعين', "", $tweet_txt );
	$tweet_txt  = str_replace('متابعيك', "", $tweet_txt );
	$tweet_txt  = str_replace('#اسأل_جدة', "", $tweet_txt );
	
		 
		 

	return $tweet_txt;

}

function get_tweet_text($string)
{
    $beg_tag =  "<div class=\"css-1dbjc4n r-156q2ks\">";
	$close_tag = "<div class=\"css-1dbjc4n r-vpgt9t\">";

    if(preg_match("($beg_tag(.*)$close_tag)siU", $string, $matching_data)){

    	$tweet_txt = strip_tags($matching_data[0],"<img>");
		$tweet_txt = ltrim($tweet_txt);
		$tweet_txt = remove_emoji(nl2br($tweet_txt));
		$tweet_txt = preg_replace('/(svg[^><]*)>/i', '$1 style="width:20px;">', $tweet_txt);
		$tweet_txt = preg_replace('/^(<br\s*\/?>)*|(<br\s*\/?>)*$/i', '',  trim($tweet_txt));


    	return  $tweet_txt;
    }
   	else 
   		return 0;
}


function get_tweet_time($string){

    if(preg_match("/(\d{1,2}):(\d{1,2})(.*)(\d{4})<\/span>/", $string, $matching_data)){
		$st = strip_tags($matching_data[0]);
		$st = explode('·', $st);
		$stime = $st[1]." ".$st[0];

		$date = strtotime($stime);
		return date('Y/m/d  H:i:s', $date); 
	}
    else
    	return false;
}


function get_replies($string)
{

	$replies_array = explode("<hr/>", $string);
    $replies = array();
   	foreach($replies_array as $reply){

   		$reply = preg_replace("(data-testid=\"(reply|retweet|like)\"(.*)</div>)siU", " ", $reply);
		$reply = strip_tags($reply,"<a><img><time>");
		
		if(preg_match("((.*)<time)siU", $reply, $match_user)){

			if(preg_match("(@(.*)<)siU", $match_user[1], $match_sn))
				$screen_name = trim($match_sn[1]); 

			if(preg_match("/\/status\/([0-9]*)/i", $match_user[1], $match_rid))
				$reply_id = $match_rid[1];
				
			if(preg_match("(\"https:\/\/pbs.twimg.com\/profile_images\/(.*)/(.*)\")siU",$match_user[1], $match_avatar)){
				$avatar = trim($match_avatar[0], '"');
				$provider_id =  $match_avatar[1];

			}else {
				$avatar = "https://abs.twimg.com/sticky/default_profile_images/default_profile_bigger.png";
			}

			if(preg_match("/<\/a>(.*)@(.*)</i", $match_user[1] , $match_fn)){
				$full_name = strip_tags($match_fn[1],"<img>");
			}
				
		}

		if(preg_match("(datetime=\"(.*)\")siU", $reply, $match_rt)){
			$date = strtotime($match_rt[1]);
			$reply_time = date('Y/m/d  H:i:s', $date); 
		}

		if(preg_match("/<\/time>(.*)/is", $reply, $match_reply)){

			$reply_text = strip_tags($match_reply[1],"<img><video>");
			$reply_text = str_replace("Replying to"," ", $reply_text);
			$reply_text = nl2br($reply_text);
			$reply_text = preg_replace('/(svg[^><]*)>/i', '$1 style="width:20px;">', $reply_text);
		}
		
	    //$images = '';
	   	// $images = strip_tags($matching_img[0],"<img>");
	   	//$reply_text = preg_replace('/pic.twitter.com\/(.*)/', '', $reply_text);

		if(isset($screen_name) && isset($reply_text)) {

			$params['screen_name'] = $screen_name;
	    	$params['name'] = $full_name;
	    	$params['provider_id']= $provider_id ?? $screen_name;
	    	$params['avatar'] = $avatar;
	    	$params['reply_text'] =  remove_emoji($reply_text);
	     	$params['reply_time'] = $reply_time;
			$params['reply_id'] = $reply_id;


	    	$replies[$reply_id] = $params;
		}
    	
  	}

	return $replies;
}



//======================================= Users FunctionS
function create_user($params){

	global $mysqli;
	$params['screen_name'] = trim($params['screen_name']);
	$result = $mysqli->query("SELECT user_id FROM `social_accounts` WHERE screen_name like '%".$params['screen_name']."%'");
	if($row = $result->fetch_assoc()){

		return $row['user_id']; 

	} else {
		
		$params['name'] = html_entity_decode($params['name']);
		$params['name'] = trim($params['name']);
		$params['name'] = mysqli_real_escape_string ( $mysqli , $params['name']);
		
		$params['screen_name'] = html_entity_decode($params['screen_name']);
		$params['screen_name'] = trim($params['screen_name']);
		//$email = $params['provider_id']."@twitter.com";
		$email = $params['screen_name']."@twitter.com";


		//mysqli_autocommit($mysqli,FALSE);
		$sql = "INSERT INTO  users (first_name,last_name,email,avatar_type,created_at)  VALUES ('".$params['name'] ."','".$params['screen_name'] ."','".$email ."','twitter',NOW())";
	

		if ($mysqli->query($sql) === TRUE) {
		    $user_id = $mysqli->insert_id;
		    //$twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, TOKEN_SECRET);
			//$user = $twitter->get('users/show',array("user_id"=> $params['provider_id'] ));

			//$params["avatar"] =  (isset($user->profile_image_url))? $user->profile_image_url : '' ;
			//$params['provider_id'] = (isset($user->id_str))? $user->id_str : $params['provider_id']; 
			//$params['name'] = (isset($user->name))? mysqli_real_escape_string ( $mysqli , $user->name) : $params['name'];
			//$params['screen_name'] = (isset($user->screen_name))? $user->screen_name : $params['screen_name']; 
			$params['country'] = ''; //(isset($user->location))? mysqli_real_escape_string ( $mysqli , $user->location) : '';
			$params['description'] = '';//(isset($user->description))? mysqli_real_escape_string ( $mysqli , $user->description) : '' ;

			//$sql = "INSERT INTO social_accounts (user_id,provider,provider_id,screen_name,name,email,avatar,country,description,created_at) VALUES ($user_id,'twitter','".$params['provider_id']."','".$params['screen_name']."','".$params['name']."','$email','".$params['avatar']."','".$params['country']."','".$params['description']."',NOW())";

			//echo $sql;

		    if($mysqli->query("INSERT INTO social_accounts (user_id,provider,provider_id,screen_name,name,email,avatar,country,description,created_at) VALUES ($user_id,'twitter','".$params['provider_id']."','".$params['screen_name']."','".$params['name']."','$email','".$params['avatar']."','".$params['country']."','".$params['description']."',NOW())") === TRUE ){
		    	
		    	return  $user_id;
		    } else {
		    	echo $mysqli->error;
		    	return   0;	
		    }
		} else {
			echo $mysqli->error;
			return 0;
		}

			
	}
}
//==================================== Create Questions
/*$params['tweet_id']
$params['user_id']
$params['tweet_text']
$params['tweet_time']
$params['replies']  = []*/

function create_question($params)
{

	global $mysqli;
	$result = $mysqli->query("SELECT id FROM `questions` WHERE source_id='".$params['tweet_id']."'");
	if($row = $result->fetch_assoc()){
		return 0; 
	} else {
		
		mysqli_autocommit($mysqli,FALSE);

		$params['tweet_text'] = mysqli_real_escape_string ( $mysqli , $params['tweet_text']);
		$params['tweet_time'] =  $params['tweet_time'];
		
		//$sql = "INSERT INTO  questions (user_id,question,source,source_id,created_at )  VALUES (".$params['user_id'].",'".$params['tweet_text']."','scrap','".$params['tweet_id']."','".$params['tweet_time']."')";
		//$mysqli->query("INSERT INTO  questions (user_id,question,source,source_id,created_at )  VALUES (".$params['user_id'].",'".$params['tweet_text']."','scrap','".$params['tweet_id']."','".$params['tweet_time']."') ");
	
		if ($mysqli->query("INSERT INTO  questions (user_id,question,source,source_id,created_at )  VALUES (".$params['user_id'].",'".$params['tweet_text']."','scrap','".$params['tweet_id']."','".$params['tweet_time']."') ") === TRUE) {

		    $q_id = $mysqli->insert_id;

		    if(isset($params['location_id']))
		    	$mysqli->query("INSERT INTO  `questions_locations` (city_id,question_id)  VALUES (".$params['location_id'].",".$q_id.") ");

		    $mysqli->query("INSERT INTO  `questions_tags` (tag_id,question_id)  VALUES (".$params['tag1_id'].",".$q_id.") ");
		    $insert_count = 0; 

		    foreach($params['replies'] as $reply){

		    	$reply_user_id = create_user($reply);
	
		    	$reply['reply_text'] = mysqli_real_escape_string ( $mysqli , $reply['reply_text']);
		    	$reply['reply_text'] = str_replace("@".$params['user_screen_name']," ",$reply['reply_text']);

				
				if($reply_user_id && $mysqli->query("INSERT INTO answers (user_id,question_id,answer,source,source_id,created_at ) VALUES (".$reply_user_id.",".$q_id .",'".$reply['reply_text']."','scrap','".$reply['reply_id']."','".$reply['reply_time']."')") === TRUE){
		    	 	$insert_count += 1; 
		    	} else {
		    		echo $mysqli->error;
		    	}

		    }
			
		    if( $insert_count > 0 ){
		    	// Commit transaction
				mysqli_commit($mysqli);
		    	return  $q_id;
		    } else {
		    	// Rollback transaction
		    	//echo mysqli_error($mysqli);
		    	echo $mysqli->error;
				mysqli_rollback($mysqli);
		    	return   0;	
		    }
		} else {
			echo $mysqli->error;
			return 0;
		}
	}
}

//========================================
//error : image question from answer text
function update_replies(){

	global $mysqli;
	$rs_replies = $mysqli->query("SELECT answers.id,
		answers.source_id as sid,
		questions.source_id ,
		questions.id as qid,
		questions.question
		FROM `answers` inner join questions on questions.id = answers.question_id 
		WHERE  answers.answer like '%pic.twitter.com%' limit 5");
		//answers.id = 316 ");


	while($reply = $rs_replies->fetch_assoc()){

		
		$url = "https://twitter.com/Ask_makkah_/status/".$reply['source_id'];
		$doc = new TwitterBotM();
		$html =$doc->http($url,"GET");
		$params = get_replies($html);

		foreach($params as $param){

			if($reply['sid'] == $param['reply_id']){

				$param['reply_text'] = str_replace("@Ask_makkah_","",$param['reply_text']);
				$param['reply_text'] = $param['reply_text'] ."<br/>".$param['reply_image'];
				$param['reply_text']  = mysqli_real_escape_string( $mysqli , $param['reply_text']);
				
				$question_text = str_replace($param['reply_image'],"",$reply['question']);
				$question_text = mysqli_real_escape_string( $mysqli , $question_text);


				$mysqli->query("UPDATE answers SET answer ='". $param['reply_text']."' where id =".$reply['id']); 
				$mysqli->query("UPDATE questions SET question ='".$question_text."' where id =".$reply['qid']);
			}

		}
		echo "======== ".$reply['id'];

	}

}

//=================================================
function update_avatar(){
	global $mysqli;
	$rs_accounts = $mysqli->query("SELECT * FROM `social_accounts` where avatar ='' limit 100");


	while($account = $rs_accounts->fetch_assoc()){
		$screen_name = trim($account['screen_name'], chr(0xC2).chr(0xA0));

		$url = "https://twitter.com/".$screen_name;
		$doc = new TwitterBotM();
		$html =$doc->http($url,"GET");

  
    	if(preg_match("(src=\"https:\/\/pbs.twimg.com\/profile_images\/(.*)\")siU", $html, $matching_data)){
    		$avatar = "https://pbs.twimg.com/profile_images/".$matching_data[1];

    		$mysqli->query("UPDATE social_accounts SET avatar ='". $avatar."' where id =".$account['id']); 
    		echo "</br> ========".$account['id'];

    	}else {

    		$avatar = "https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png";
    		$mysqli->query("UPDATE social_accounts SET avatar ='". $avatar."' where id =".$account['id']); 
    		echo "</br> default ========".$account['id'];

    	}



	}
				
}



 //===================================================== CURL 

$tweet =  $_POST["tweet"];
$replies = $_POST["replies"];
$screen_name = $_POST["user"];
$tweet_id = $_POST["tweet_id"];

$tweet_text = get_tweet_text($tweet);
$tweet_time = get_tweet_time($tweet);

$re_array=  get_replies($replies);

$params['location_id'] = ($_POST["city_id"] != '1')? $_POST["city_id"]  :  null ;
$params['tag1_id'] = $_POST["tag_id"];
$params['tweet_id'] = $tweet_id;
$params['user_id'] =  $_POST["user_id"];


if(count($re_array) > 0){

	$params['tweet_text'] = $tweet_text;
	$params['tweet_time'] = $tweet_time;
	$params['replies']  = $re_array;
	$params['user_screen_name'] = $screen_name;

	if (!$params['tweet_text']  || 
		strpos($params['tweet_text'], 'حان الآن وقت صلاة') !== false  ||
		strpos($params['tweet_text'], 'حساب تطوعي لمساعدتكم') !== false ) {

		$response['status'] = 0 ;
		$response['error'] =  "NOT Questions" ;
		$response['source_id'] =  $tweet_id;
		echo json_encode($response);
		//$mysqli->query("UPDATE `askboot_crawling_tweets` SET status = 0 WHERE tweet_id = ".$tweet_id);

	}else {

		$qid = create_question($params);
		if($qid){
			$response['status'] = 1 ;
			$response['source_id'] =  $tweet_id;
			$response['inserted_rowid'] = $qid ;
			echo json_encode($response);
		}else {
			$response['status'] = 0 ;
			$response['source_id'] =  $tweet_id;
			$response['error'] = "EXITS OR NOT INSERTED" ;
			echo json_encode($response);
		}
		
	}
	
} else {

	$response['status'] = 0 ;
	$response['error'] =  "Have NOT Re" ;
	$response['source_id'] =  $tweet_id;
	echo json_encode($response);
	//$mysqli->query("UPDATE `askboot_crawling_tweets` SET status = 0 WHERE tweet_id = ".$tweet_id);
}



exit;


$connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
if (!$connected){
    echo "======= NO INETRNET ======";
    exit;
}
fclose($connected);




// for loading more = https://twitter.com/i/Ask_makkah_/conversation/1071763454980186112?include_available_features=1&include_entities=1&reset_error_state=false&max_position=DAACDwABCgAAABEO4FYe39bgAA7gTFgRVuABDuAmTYrX4AEO4BUDfFfgAA7gAOcFl4AADt_6YePW4AIO3-szXddAAg7f5vzm1sAADt_e0pAX4AAO39iUrZaQAQ7f09ALVtACDt_CMO_W0AEO37rJ8lbQAQ7fuZCmF0AADt-1DC0XgAAO37PwfZaQAA7fs8lYF-ABCAADAAAAAQIABAAAAA





?>