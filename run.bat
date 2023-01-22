rem Monitoring multiple channels
@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchpresents
@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchmusic

rem The default no tail, recording both video and audio.
rem @start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchpresents

rem Audio option , add `a` or `A` on tail to record audio only.
rem @start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchmusic A

rem Video option , add `v` or `V` on tail to record video only.
rem @start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchgaming v

rem A/V option , add `AV` or `av` on tail to record both.
rem @start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchgaming av