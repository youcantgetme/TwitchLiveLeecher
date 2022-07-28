rem The default no tail, recording both video and audio.
@start %~dp0php\php.exe %~dp0live_leecher.php TwitchChannel1

rem Audio option , add `a` or `A` on tail to record audio only.
rem @start %~dp0php\php.exe %~dp0live_leecher.php TwitchChannel2 A

rem Video option , add `v` or `V` on tail to record video only.
rem @start %~dp0php\php.exe %~dp0live_leecher.php TwitchChannel3 v

rem A/V option , add `AV` or `av` on tail to record both.
rem @start %~dp0php\php.exe %~dp0live_leecher.php TwitchChannel4 av