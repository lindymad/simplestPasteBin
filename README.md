# Simplest Paste Bin
The simplest paste bin

To install just copy the files to your webroot and modify the SAVEPATH in pastebin.php to point to where you want the pastes to be saved (make sure the webserver has write permissions), then go to http://your.domain.com/pastebin.php

Note: this was thrown together in about an hour for https://www.reddit.com/r/selfhosted/comments/wontpt/looking_for_a_extremely_light_pastebin/, so the code is pretty raw :)

**Important** : Don't forget to change the SAVEPATH in pastebin.php. By default it saves in `/tmp` but most OSes will clear that folder every now and then (e.g. on reboot), so it's important to change it to somewhere that is outside of `/tmp`
