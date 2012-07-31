<div id="mp3_div"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
<script src="/app/mediaplayer/jwplayer.js"></script>
<script type="text/javascript">
jwplayer("mp3_div").setup({
    flashplayer: "/app/mediaplayer/player.swf",
        file: '<?php echo escape_singlequote($play) ?>',
        width: <?php echo $mp3_width ?>,
        events: {
            onReady: function(){
                jwplayer().play();
            }
        }
    });
</script>
