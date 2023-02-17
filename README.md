# Twitch Live Leecher

A simple PHP script to emulate browser watching live streams, download HLS via ffmpeg.

# installation

- Modify `run.bat`, add channels
- Execute `run.bat`
- The result will locate in VOD folder

# Options

IDLE_TIME - 120 seconds default, Probing interval.
VIDEO_CONTAINER - MP4/MKV , MKV(Matroska) format could still read after crash, can convert to MP4 via OBS.
FORCE_48000_AUDIO - 0/1 Force audio convert to 48K to prevent A/V desync cause by some not 48K sample rate AD, additional CPU required.

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
Then modify `undefined` following OAUTH_TOKEN with your token at the second line of live_leecher.php.
![image](https://github.com/youcantgetme/TwitchLiveLeecher/blob/master/auth-token.png)


# Twitch Live Leecher
用ffmpeg同時錄多個Twitch直播的小語法

# 設定

- 右鍵>編輯 修改 `run.bat` 成要錄影的頻道
- 執行 `run.bat` 開台後會自動錄影
- 直播存檔在VOD資料夾裡

# 選項

IDLE_TIME - 監控開台頻率的間隔, 預設120秒
VIDEO_CONTAINER - MP4/MKV , MKV(Matroska)格式下意外中斷或當機後的檔案仍然可以讀取, 可以用OBS轉成MP4
FORCE_48000_AUDIO - 0/1 避免廣告音訊不是48K取樣造成影音不同步, 需額外用CPU而預設關閉

# 設定頻道

範例, 同時錄twitchpresents與twitchmusic頻道

`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchpresents`

`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchmusic`


# 錄影錄音選項

`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchpresents`

- 預設不加參數, 影音都會錄



`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchmusic a`

- 只錄聲音, 尾巴加 `a` 或 `A` 參數, 檔案很小,適合搭配被消音的VOD用



`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchgaming v`

- 只錄影像, 尾巴加 `v` 或 `V` 參數



`@start "" "%~dp0php\php.exe" "%~dp0live_leecher.php" twitchgaming av`

- 影音都錄, 與預設不加參數相同

# 如何找到金鑰

Twitch Live Leecher 可以用訂閱者金鑰跳過廣告, 以Chrome為例 打開Twitch頁面後按F12叫出開發工具然後依下圖找到金鑰
然後在 live_leecher.php 第二行 找到OAUTH_TOKEN後的 `undefined` 修改成剛剛取得的值>存檔>重開run.bat
![image](https://github.com/youcantgetme/TwitchLiveLeecher/blob/master/auth-token.png)