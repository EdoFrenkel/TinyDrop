TinyDrop
========

# Optimize Unity3D Atlases automatically
TinyDrop is a PHP script which use Dropbox API and TinyPNG API to optimize automatically your Unity3D atlases

## Installation
1. upload the project to your Server
2. go to https://www.dropbox.com/developers/apps and click the Create app button
..* Dropbox API App
..* Files and datastore
..* No
..* Specific file types
..* Images
..* Enter your app name
..* Create app
3. in the settings tab of the Dropbox app you've got-  App key and Secret key
4. go to https://tinypng.com/developers sign and get you're tinyPNG API key
5. go to the project file: tinydrop.php
6. under $config you should set all the data
7. under $name add the names of the folders where the atlases sits
8. upload the new tinydrop.php to the server
9. in your browser go to the root folder of this project (index.php)
10. you should see authorization request. click on it and you'll be redirect to Dropbox authorize. please do :)
11. thats it...now you can choose the folders you want to compress

few things to notice:
* the script was build for the name convention of 2dToollkit, but you can change it easily
* you can choose the atlas folders you want to compress
* before the script start the process it checks whether the every specific Dropbox folder actually exist
* if an atlas have been compressed already by the script, it won't compress him again. you don't want to compress the same atlas few times by mistake.
* smile :)

Issues:
* If you've got graphics with gradiant opacity it won't compress well, so you can put all those images in one atlas that will be left alone
* If you find more issues please let me know...i'll update the docs
