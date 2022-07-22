<?php
set_time_limit(0);
define('OAUTH_TOKEN','undefined'); //replace 'undefined' with your oauth_token if you are subscriber to avoid AD , this token could only obtain via 'auth-token' in the Cookie located in Application tab of browser developer tools.
define('IDLE_TIME',180); //interval between check ,unit second.
define('VIDEO_CONTAINER','mp4'); //(mkv)Matroska file could still read after accidentally crash.
define('FFMPEG_OPTIONS','-movflags faststart'); //faststart works on MP4 only.
define('LOG_FILE','log.txt');
define('VOD_FOLDER','VOD');
define('FORCE_44100_AUDIO',0); //set 1 to prevent AD in the middle cause A/V unsynchronized because different sample rate
define('TIMEZONE',8); //GMT +8
define('VER','1.10');

if(empty($argv[1]))exit('No CHANNEL assigned');
$channel=$argv[1];
$first_run=true;
$session_ts=0;
$lastest_vod_ts=0;
$token_timeout_ts=0;
$disconnect_check=0;
$token_request=false;
$ffmpeg_arg='';
$filename_append='';
$record_mode='';
$audio=true;
$video=true;
$token_status='';
$timezone_offset=TIMEZONE*3600;

if(!is_dir(dirname(__FILE__).DIRECTORY_SEPARATOR.VOD_FOLDER))mkdir(dirname(__FILE__).DIRECTORY_SEPARATOR.VOD_FOLDER);

//AV args will be ignored if codec assigned in FFMPEG_OPTIONS
if(strpos(FFMPEG_OPTIONS,'-c')===false && strpos(FFMPEG_OPTIONS,'codec')===false)
{
	//parse input args , leaves default setting if no args 
	if(isset($argv[2]) && !empty($argv[2]))
	{
		stristr($argv[2],'a')!==false?$audio=true:$audio=false;
		stristr($argv[2],'v')!==false?$video=true:$video=false;
		if(stristr($argv[2],'c')!==false)
		{
			$video=false;
			$audio=false;
		}
	}

	if($audio && $video) 
	{
		//default , record both audio and video
		$ffmpeg_arg='-c copy';
		if(FORCE_44100_AUDIO)
			$ffmpeg_arg='-c:v copy -c:a aac -b:a 160k -ar 44100';
	}
	elseif($audio)
	{
		//audio only
		$ffmpeg_arg='-vn -c:a copy';
		if(FORCE_44100_AUDIO)
			$ffmpeg_arg='-vn -c:a aac -b:a 160k -ar 44100';
		$filename_append='_audio_only';
		$record_mode=' *Audio only*';	
	}
	elseif($video)
	{
		//video only
		$ffmpeg_arg='-an -c:v copy';
		$filename_append='_video_only';
		$record_mode=' *Video only*';	
	}
}
exec('title Twitch Live leecher v'.VER.' : '.$channel.' [idle]');
while(1)
{
	if($disconnect_check>0)
	{
		sleep($disconnect_check);
		$disconnect_check*=3;
		if($disconnect_check>IDLE_TIME)
		$disconnect_check=0;
	}
	elseif(!$first_run)sleep(IDLE_TIME);
	$first_run=false;

	if($lastest_vod_ts==0)
		exec('title Twitch Live leecher v'.VER.' : '.$channel.$record_mode.' [idle] '.$token_status);
	else
		exec('title Twitch Live leecher v'.VER.' : '.$channel.$record_mode.' [idle] , lastest VOD recorded at '.date('Ymd H:i:s',$lastest_vod_ts).'. '.$token_status);
		
	//checking channel status
	$oauth_token=OAUTH_TOKEN;
	if($oauth_token=='undefined')
		$token_status=' (not log in) ';
	elseif(strlen(OAUTH_TOKEN)!=30 || !ctype_alnum(OAUTH_TOKEN))
	{
		$token_status=' (*token invalid) ';
		$oauth_token='undefined';
	}
	else
		$oauth_token='OAuth '.OAUTH_TOKEN;
	
	if(time()-21600 > $token_timeout_ts)
	{
		$token_timeout_ts=time();
		$token_request=token_check($channel,$oauth_token);
		if($oauth_token!='undefined')
		{
			if($token_request===false || strpos($token_request,'"user_id\":null,')!==false)
			{
				log_msg('[EROR] Server request failed, please check channel '.$channel.' and OAUTH_TOKEN is valid , retrying with guest');
				//retrying with guest 
				$oauth_token='undefined';
				$token_status=' (*token invalid) ';
				$token_request=token_check($channel,$oauth_token);
			}
			else
				$token_status=' (token valid) ';
		}
		exec('title Twitch Live leecher v'.VER.' : '.$channel.$record_mode.' [idle] '.$token_status);
	}
	if($token_request===false)
	{
		log_msg('[EROR] Server request failed, please check channel '.$channel.' and OAUTH_TOKEN is valid');
		continue;
	}

	if($first_run)log_msg('[AUTH] Token acquired, begin listening '.$channel);
	$current_ts=$session_ts=time()+$timezone_offset;
	echo date('Ymd H:i:s',$current_ts).' [INFO] Listening '.$channel.PHP_EOL;
	
	$json=json_decode($token_request,true);
	if(!isset($json['data']['streamPlaybackAccessToken']['signature']) || empty($json['data']['streamPlaybackAccessToken']['signature']))
	{
		log_msg('[EROR] Unable to get correct API response of '.$channel);
		continue;
	}
	$token=urlencode($json['data']['streamPlaybackAccessToken']['value']);
	
	//getting M3U8 URL
	$usher=@file_get_contents('https://usher.ttvnw.net/api/channel/hls/'.$channel.'.m3u8?allow_source=true&fast_bread=true&p=11'.mt_rand(10000,99999).'&play_session_id=6b9ddd91630dbe31f54e5c41c8b190e5&player_backend=mediaplayer&playlist_include_framerate=true&reassignments_supported=true&sig='.$json['data']['streamPlaybackAccessToken']['signature'].'&supported_codecs=vp09&token='.$token.'&cdm=wv&player_version=1.2.0');
	if($usher===false)continue; //consider channel offline
	$https_pos_begin=strpos($usher,'https');
	$https_pos_end=strpos($usher,'.m3u8',$https_pos_begin);
	$m3u8_url=substr($usher,$https_pos_begin,$https_pos_end-$https_pos_begin);
	
	if(empty($m3u8_url))
	{
		log_msg('[EROR] Not able to get correct M3U8 URL of '.$channel);
		continue;
	}
	$m3u8_url.='.m3u8';
	
	if(strpos(file_get_contents($m3u8_url),',Amazon|')!==false) //M3U8 contains AD
	{
		$current_ts=time()+$timezone_offset;
		exec('title Twitch Live leecher v'.VER.' : '.$channel.$record_mode.' [Playing AD] '.$token_status);
		log_msg('[INFO] Bypassing AD');
		while(true)
		{
			echo '.';
			sleep(4);
			if(strpos(file_get_contents($m3u8_url),',Amazon|')===false)break;			
		}
		echo PHP_EOL;
	}
	
	//downloading VOD via ffmpeg
	exec('title Twitch Live leecher v'.VER.' : '.$channel.$record_mode.' [Recording] , Press "Q" to stop recording. '.$token_status);
	$current_ts=time()+$timezone_offset;
	log_msg('[INFO] Record session '.$session_ts.' of '.$channel.$record_mode.' begins');
	exec ('ffmpeg -n -i '.$m3u8_url.' '.FFMPEG_OPTIONS.' '.$ffmpeg_arg.' '.dirname(__FILE__).DIRECTORY_SEPARATOR.VOD_FOLDER.DIRECTORY_SEPARATOR.$channel.'-'.date('Ymd_His',$current_ts).$filename_append.'.'.VIDEO_CONTAINER);
	echo '====================================='.PHP_EOL;
	$lastest_vod_ts=$current_ts=time()+$timezone_offset;
	log_msg('[INFO] Record session '.$session_ts.' of '.$channel.$record_mode.' ends with '.date('H:i:s',$current_ts-$session_ts));
	exec('title Twitch Live leecher v'.VER.' : '.$channel.$record_mode.' [idle] , lastest VOD recorded at '.date('Ymd H:i:s',$lastest_vod_ts).'. '.$token_status);
	$disconnect_check=1;
}
function log_msg($msg=NULL)
{
	if(empty($msg))return false;
	$msg=date('Ymd H:i:s',time()+TIMEZONE*3600).' '.$msg.PHP_EOL;
	echo $msg;
	file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.LOG_FILE,$msg,FILE_APPEND);
}
function token_check($channel,$oauth_token)
{
	$opts = array('http'=>
		array(
			'method' =>'POST',
			'header' =>['Content-Type: text/plain;charset=UTF-8','Client-ID: kimne78kx3ncx6brgo4mv6wki5h1ko','Authorization: '.$oauth_token],
			'content'=>'{"operationName":"PlaybackAccessToken_Template","query":"query PlaybackAccessToken_Template($login: String!, $isLive: Boolean!, $vodID: ID!, $isVod: Boolean!, $playerType: String!) {  streamPlaybackAccessToken(channelName: $login, params: {platform: \"web\", playerBackend: \"mediaplayer\", playerType: $playerType}) @include(if: $isLive) {    value    signature    __typename  }  videoPlaybackAccessToken(id: $vodID, params: {platform: \"web\", playerBackend: \"mediaplayer\", playerType: $playerType}) @include(if: $isVod) {    value    signature    __typename  }}","variables":{"isLive":true,"login":"'.$channel.'","isVod":false,"vodID":"","playerType":"site"}}'
		)
	);
	$context  = stream_context_create($opts);
	return @file_get_contents('https://gql.twitch.tv/gql', false, $context);
}
?>
