# Simplest Paste Bin
The simplest paste bin

To install, clone the project to your webroot, then copy the settings.ini.sample file to settings.ini and set the `savePath` to the folder where you want the pastes to be saved (make sure the webserver has write permissions to that folder). 

**Important** : The default savePath is `/tmp` but most OSes will clear that folder every now and then (e.g. on reboot), so it's important to change it to somewhere that is outside of `/tmp`

When you are ready, go to http://your.domain.com/simplePasteBin/ and paste away! 

If you do not specify a title, the string "spb" plus an incremental number based on existing pastes will be used. (Note that means that if you create and delete a paste, the number will be re-used.) Back and forward slashes will be removed in any titles, as the title is used for the filename on your server where the paste is saved.

You can hold down shift when deleting pastes to avoid the confirmation prompt.

Note: this was thrown together in about an hour for https://www.reddit.com/r/selfhosted/comments/wontpt/looking_for_a_extremely_light_pastebin/, so the code is pretty raw :)

## Security Note
This code is not designed to be facing the public internet and has little in the way of security hardening. If you want it to be, you should at a minimum, protect it with a username and password using [basic authentication](https://en.wikipedia.org/wiki/Basic_access_authentication), and ensure you have SSL setup. Even better, do some hardening and send a pull request!
