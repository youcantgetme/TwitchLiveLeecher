rem The default no tail, recording both video and audio.
@start php\php.exe live_leecher.php test1

rem Audio option , add `a` or `A` on tail to record audio only.
@start php\php.exe live_leecher.php test2 A

rem Video option , add `v` or `V` on tail to record video only.
@start php\php.exe live_leecher.php test3 v

rem A/V option , add `AV` or `av` on tail to record both.
@start php\php.exe live_leecher.php test4 av