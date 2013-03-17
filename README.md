MediaStreamer
============================

MediaStreamer is a light, open source, web-based media server. It allows you to access your music, videos and podcast anywhere in the world with you navigator. It is also comptatible with subsonic apps, allowing you to listen your music on smartphone.

And obviously it scrobbles music on last.fm.

![Mediastreamer](http://img20.imageshack.us/img20/1517/mediastreamer.png)

Quick Start
-----------------
* Clone the repo, `git clone git://github.com/cyprieng/MediaStreamer.git` or [download the latest release](https://github.com/cyprieng/MediaStreamer/zipball/master).
* Next you need to redirect `URL MEDIASTREAMER/rest/method.view` to `URL MEDIASTREAMER/rest/api.php?api=method`

####LIGHTTPD
	var.msdir = "URL TO MEDIASTREAMER"
	url.rewrite-once = (
	      "^"+msdir+"rest/([A-Za-z_0-9-]+).+\?(.*)" => msdir+"rest/api.php?api=$1&$2""
	)


####APACHE
	Options +FollowSymlinks
	RewriteEngine on
	RewriteRule rest/([A-Za-z_0-9-]+)\.view  rest/api.php?api=$1 [QSA]

Copyright and license 
---------------------

[![Build Status](http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png)](http://creativecommons.org/licenses/by-nc-sa/3.0/deed.en)  
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.