<style type="text/css">
*{max-width:400px;margin:0;padding:0;font-family:Tahoma;font-size:10pt;}
#wrapper{height:45px;border-bottom:5px solid #ddd;}
#status{width:232px;height:50px;float:left;color:#aaa;}
#playing_name > marquee{/*overflow:hidden;*/height:30px;font-size:24px;color:black;}
#controls{width:168px;height:50px;float:right;}
#playlist{word-wrap:break-word;height:350px;overflow:auto;}
a.button{display:block;float:left;height:18px;background:url(<?php echo base_url('images/cbuttons.png') ?>);}
#bPrev{width:22px;background-position:-1px 0px;}
#bPlay{width:23px;background-position:-23px 0px;}
#bPause{width:23px;background-position:-46px 0px;}
#bStop{width:23px;background-position:-69px 0px;}
#bNext{width:22px;background-position:-92px 0px;}
#bLoop{width:27px;background-position:-114px 0px;}
#bShuf{width:27px;background-position:-141px 0px;}
#bPrev:active {width:22px;background-position:-1px -18px;}
#bPlay:active {width:23px;background-position:-23px -18px;}
#bPause:active {width:23px;background-position:-46px -18px;}
#bStop:active {width:23px;background-position:-69px -18px;}
#bNext:active {width:22px;background-position:-92px -18px;}
#progress,#volume{width:168px;height:8px;margin:3px 0;background:#ddd;position:relative;}
.on{height:8px;}
#progress .on{background:red;}
#volume .on{background:blue;}
.layer{display:none;position:absolute;top:0;left:0;text-align:center;font-size:3pt;color:white;width:168px;}
#playlist a{text-decoration:none;color:#777;}
#playlist li{padding:3px;overflow-y:hidden;height:16px;}
#playlist li.even{background:#eee;}
#playlist li.playing {font-weight:bold;background:#777;}
#playlist li.playing a{color:white;}
</style>
<audio id="mediaplayer" preload="none"><!-- controls="controls"-->
Your browser does not suppoer the audio element.
</audio>
<div id="wrapper">
<div id="status">
	<span id="playing_status">[Not loaded]</span>
	<span id="time">0</span>
	<div id="playing_name"></div>
</div>
<div id="controls">
	<div id="progress" onmousedown="seek();"><div class="on"></div><div class="layer">PROGRESS</div></div>
	<div id="volume" onmousedown="changeVolumeMoving();"><div class="on"></div><div class="layer">VOLUME</div></div>
	<div id="buttons">
		<a href="#prev" onclick="prev();" id="bPrev" class="button"></a>
		<a href="#play" onclick="play();" id="bPlay" class="button"></a>
		<a href="#pause" onclick="pause();" id="bPause" class="button"></a>
		<a href="#stop" onclick="stop();" id="bStop" class="button"></a>
		<a href="#next" onclick="next();" id="bNext" class="button"></a>
		<a href="#loop" onclick="loop();" id="bLoop" class="button"></a>
		<a href="#shuffle" onclick="shuffle();" id="bShuf" class="button"></a>
	</div>
</div>
<p style="clear:both"></p>
</div>
<ul id="playlist"></ul>
<script type="text/javascript" src="<?php echo base_url('script/jquery-latest.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('script/json2.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('script/keyDownHandler.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('script/HTML5AudioPlayer.js') ?>"></script>
<script type="text/javascript">
$(function(){
	$('#playing_status').click(function(){
		$.get('<?php echo base_url($loadfile) ?>?'+(new Date().getTime()),{},function(data,e,xhr){buildingList(xhr.responseXML);load();},'xml');
	}).css('cursor','pointer').click();

	$('#volume').mousedown(changeVolume);
});
</script>

