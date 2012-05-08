document['onkeydown'] = detectKeyDown;
function detectKeyDown(e) {
    var evt = e || window.event;
    var p;
    if( e.ctrlKey ) return;
    if( e.altKey ) return;

    if( parent.player ) 
        p = parent.player.document.getElementById('mediaplayer');
    else if( typeof(w) != 'undefined' )
        p = w.document.getElementById('mediaplayer');
	else
		p = window;
    
    if( evt.keyCode == 90 ) {p.sendEvent('prev', 'true');} // z
    if( evt.keyCode == 88 ) {p.sendEvent('play', '2');} // x
    if( evt.keyCode == 67 ) {p.sendEvent('play', '2');} // c
    if( evt.keyCode == 86 ) {p.sendEvent('stop', 'true');} // v
    if( evt.keyCode == 66 ) {p.sendEvent('next', 'true');} // b
    if( evt.keyCode == 65 ) {refresh_player('listing.php?r=1');} // a
    return true;
}

