# Twitch Live Leecher

A simple PHP script to emulate browser watching live streams, download HLS via ffmpeg.

# installation

- Modify `run.bat`, add channels
- Execute `run.bat`
- The result will locate in VOD folder

# Options

- Check interval
The default listening interval is 180 seconds, change this via IDLE_TIME.

# Config by channels 
Monitoring multiple channels
`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchpresents`
`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchmusic`

If no arguments on tail, setting from script will be use.


`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchpresents`

- The default no tail, recording both video and audio.



`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchmusic a`

- Audio option , add `a` or `A` on tail to record audio only, useful when preventing from DMCA auto mute purpose.



`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchgaming v`

- Video option , add `v` or `V` on tail to record video only.



`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchgaming av`

- A/V option , add `AV` or `av` on tail to record both.

# How to find auth-token 
Live Leecher can bypass Ads with subscriber's account token, to get your own token , press F12 on browser to open Dev tool and locate token with picture below.
Then modify `undefined` following OAUTH_TOKEN with your token at the third line of live_leecher.php.
![image](https://github.com/youcantgetme/TwitchLiveLeecher/blob/master/auth-token.png)