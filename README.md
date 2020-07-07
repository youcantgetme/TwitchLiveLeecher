# Twitch Live Leecher

A simple PHP script to emulate browser watching live stream, download HLS via ffmpeg.

# installation

- Modify `run.bat`, add or edit channel
- Execute `run.bat`
- The result will locate in VOD folder

# Options

- Check interval
The default listening interval is 180 seconds, change this via IDLE_TIME.

- Audio only
Set AUDIO_ONLY to 1 to record audio only, useful when just preventing from DMCA auto mute purpose.

# Config by channel 

If no arguments on tail, setting from script will be use.


`@start php\php.exe live_leecher.php test`

- The default no tail, leaves AUDIO_ONLY to decide.



`@start php\php.exe live_leecher.php test a`

- Audio option , add `a` or `A` on tail to record audio only.



`@start php\php.exe live_leecher.php test v`

- Video option , add `v` or `V` on tail to record video only.



`@start php\php.exe live_leecher.php test av`

- A/V option , add `AV` or `av` on tail to record both.
