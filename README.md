# Twitch Live Leecher

A simple PHP script to emulate browser watching live stream , download HLS via ffmpeg.

# installation

- Download ffmpeg build from [zeranoe](https://ffmpeg.zeranoe.com/builds/)
- Extract `ffmpeg.exe` to the folder , together with `run.bat`
- Modify `run.bat` , add or edit channel
- Execute `run.bat`
- The result will locate in VOD folder

# Notes

The default listening interval is 180 seconds , change this via IDLE_TIME at the live_leecher.php
