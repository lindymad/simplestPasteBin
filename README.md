# Simplest Paste Bin
The simplest paste bin

To install, copy the simplePasteBin folder to your webroot, then copy the settings.ini.sample file to settings.ini and set the `savePath` to the folder where you want the pastes to be saved (make sure the webserver has write permissions to that folder). 

**Important** : The default savePath is `/tmp` but most OSes will clear that folder every now and then (e.g. on reboot), so it's important to change it to somewhere that is outside of `/tmp`

When you are ready, go to http://your.domain.com/simplePasteBin/ and paste away! If you do not specify a title, a random string will be used. `/` and `\` will be removed in any titles, as the title is used for the filename on your server where the paste is saved.

Note: this was thrown together in about an hour for https://www.reddit.com/r/selfhosted/comments/wontpt/looking_for_a_extremely_light_pastebin/, so the code is pretty raw :)
