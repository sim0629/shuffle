<div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
<script type="text/javascript" src="<?php echo $swfobject ?>"></script>
<script type="text/javascript">
    var so = new SWFObject("<?php echo $swf_mp3 ?>","mediaplayer","<?php echo $mp3_width ?>","<?php echo $mp3_height ?>","7");
    so.addVariable("repeat","always");
    so.addVariable("width","<?php echo $mp3_width ?>");
    so.addVariable("height","<?php echo $mp3_height ?>");
    so.addVariable("displayheight","0");
    so.addVariable("file","<?php echo $loadfile ?>?" + (new Date().getTime()));
    so.addVariable("autostart","true");
    so.addVariable("shuffle","<?php echo $shuf ?>");
    so.addVariable("skin","simple.swf");
    so.addVariable("playlist","bottom");
    so.addVariable("playlistsize","380");
    so.addVariable("lightcolor","cc0022");
    so.addVariable("backcolor","eeeeee");
    so.addVariable("frontcolor","888888");
    so.addVariable("dock","false");
    so.write("mp3_div");
</script>

