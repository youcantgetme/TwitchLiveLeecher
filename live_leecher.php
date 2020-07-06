<?php
set_time_limit(0);
define('VOD_FOLDER','VOD');
define('IDLE_TIME',180); //seconds
define('LOG_FILE','log.txt');
if(empty($argv[1]))exit('No CHANNEL assigned');
$channel=$argv[1];
$first_run=true;
$session_ts=0;
$current_ts=time()+28800;
$lastest_vod_ts=0;
exec('title Twitch Live leecher: '.$channel.' [idle]');
while(1)
{
	if(!$first_run)sleep(IDLE_TIME);
	$first_run=false;
	if($lastest_vod_ts==0)
		exec('title Twitch Live leecher: '.$channel.' [idle]');
	else
		exec('title Twitch Live leecher: '.$channel.' [idle] , lastest VOD recorded at '.date('Ymd H:i:s',$lastest_vod_ts));
	echo date('Ymd H:i:s',$current_ts).' [INFO] Listening '.$channel.PHP_EOL;
	
	//checking channel status
	$current_ts=$session_ts=time()+28800;
	$token_request=@file_get_contents('https://api.twitch.tv/api/channels/'.$channel.'/access_token?oauth_token=undefined&need_https=true&platform=web&player_type=site&player_backend=mediaplayer&client_id=kimne78kx3ncx6brgo4mv6wki5h1ko');
	if($token_request===false)
	{
		$msg=date('Ymd H:i:s',$current_ts).' [EROR] Not able to get API of '.$channel.' , maybe incorrect channel spell or got banned?'.PHP_EOL;
		echo $msg;
		continue;
	}
	
	$json=json_decode($token_request,true);
	if(!isset($json['sig']) || empty($json['sig']))
	{
		$msg=date('Ymd H:i:s',$current_ts).' [EROR] Not able to get correct API response of '.$channel.PHP_EOL;
		echo $msg;
		file_put_contents(LOG_FILE,$msg,FILE_APPEND);
		continue;
	}
	$token=urlencode($json['token']);
	
	//getting M3U8 URL
	$usher=@file_get_contents('https://usher.ttvnw.net/api/channel/hls/'.$channel.'.m3u8?allow_source=true&fast_bread=true&p=1151682&play_session_id=6b9ddd91630dbe31f54e5c41c8b190e5&player_backend=mediaplayer&playlist_include_framerate=true&reassignments_supported=true&sig='.$json['sig'].'&supported_codecs=avc1&token='.$token.'&cdm=wv&player_version=2.23.8');
	if($usher===false)continue; //consider channel offline
	$https_pos_begin=strpos($usher,'https');
	$https_pos_end=strpos($usher,'.m3u8',$https_pos_begin);
	$m3u8_url=substr($usher,$https_pos_begin,$https_pos_end-$https_pos_begin);
	if(empty($m3u8_url))
	{
		$msg=date('Ymd H:i:s',$current_ts).' [EROR] Not able to get correct M3U8 URL of '.$channel.PHP_EOL;
		echo $msg;
		file_put_contents(LOG_FILE,$msg,FILE_APPEND);
		continue;
	}
	$m3u8_url.='.m3u8';
	if(!is_dir(VOD_FOLDER))mkdir(VOD_FOLDER);
	
	//downloading VOD via ffmpeg
	exec('title Twitch Live leecher: '.$channel.' [Recording] , Press "Q" to stop recording');
	$current_ts=time()+28800;
	$msg=date('Ymd H:i:s',$current_ts).' [INFO] Record session '.$session_ts.' of '.$channel.' begins'.PHP_EOL;
	echo $msg;
	file_put_contents(LOG_FILE,$msg,FILE_APPEND);
	exec ('ffmpeg -i '.$m3u8_url.' -c copy '.VOD_FOLDER.DIRECTORY_SEPARATOR.$channel.'-'.date('Ymd_His',$current_ts).'.mp4');
	echo '====================================='.PHP_EOL;
	$lastest_vod_ts=$current_ts=time()+28800;
	$msg=date('Ymd H:i:s',$current_ts).' [INFO] Record session '.$session_ts.' of '.$channel.' ends'.PHP_EOL;
	echo $msg;
	file_put_contents(LOG_FILE,$msg,FILE_APPEND);
	exec('title Twitch Live leecher: '.$channel.' [idle] , lastest VOD recorded at '.date('Ymd H:i:s',$lastest_vod_ts));
}
?>
