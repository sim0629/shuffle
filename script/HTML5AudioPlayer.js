function History(origLen, factor) {
	this.list = [];
	this.factor = factor;
	this.maxLength = origLen * factor;

	this.modify = function(len) {
		this.maxLength = len * this.factor;
	};

	this.push = function(index) {
		if( this.list.indexOf(index) != -1 )
			return;
		this.list.push(index);
		if( this.list.length > this.maxLength )
			this.list.shift(1);
	};

	this.isin = function(index) {
		return this.list.indexOf(index) != -1;
	};

	this.prev = function(index, fallback) {
		var idx = this.list.indexOf(index);
		if( idx == -1 || idx == 0 )
			return fallback;
		else
			return this.list[idx - 1];
	};

	this.next = function(index) {
		var idx = this.list.indexOf(index);
		if( idx == this.list.length - 1 )
			return -1;
		else
			return this.list[idx + 1];
	};

	this.del = function(index) {
		var deleted = new Array();
		for( var i in this.list ) {
			if( this.list[i] > index ) {
				this.list[i]--;
			} else if( this.list[i] == index ) {
				delete this.list[i];
			}
		}
		deleted = deleted.sort(function(a,b){return (a>b)?1:-1;});
		for( var i in deleted ) {
			this.list.splice(deleted[i],1);
		}
	};
}

var list = new Array();
var currentIndex = 0;
var bShuffle = false;
var intervalCurrent = null;
var barWidth = $('#progress').width();
var volume = 0.8;
var timerDuration = 30;
var historyFactor = 0.3;
var historyManager = new History(list.length, historyFactor);
Object.prototype.sendEvent = function(method, argument) {
	if( typeof(method) == 'string' ) {
		if( method == 'play' ) {
			if( $('#mediaplayer')[0].paused )
				play();
			else
				pause();
		} else {
			eval( method + '()' );
		}
	}
};

function displayTime(){
	var time = $('#mediaplayer')[0].currentTime;
	var second = Math.floor(time%60);
	var minute = Math.floor(Math.floor(time)/60);
	var hour = Math.floor(minute/60);
	minute = minute % 60;
	$('#time').html( ((hour/10 < 1)?"0":"") + hour + ":" + ((minute/10 < 1)?"0":"") + minute + ":" + ((second/10 < 1)?"0":"") + second );

	var total = $('#mediaplayer')[0].duration;
	$('#progress > .on').css('width', (barWidth * (time/total)) + 'px');
}
function play() {
	$('#mediaplayer')[0].play();
	$('#playing_status').html('[PLAY]');
//	$('#playing_name').marquee();
	if(intervalCurrent)
		clearInterval(intervalCurrent);
	intervalCurrent=setInterval(displayTime,timerDuration);
	changeVolumeTo(volume);
}
function pause(){$('#mediaplayer')[0].pause();$('#playing_status').html('[PAUSE]');clearInterval(intervalCurrent);}
function stop(){load(currentIndex);pause();$('#playing_status').html('[STOP]');}
function prev(){
	load( historyManager.prev( currentIndex, (currentIndex-1+list.length)%list.length ) );
}
function next() {
	if($('#mediaplayer').attr('loop')) {
		load(currentIndex);
		return;
	} else if(!bShuffle) {
		load((currentIndex+1)%list.length);
	} else {
		var nextIndex = historyManager.next( currentIndex );
		if( nextIndex == -1 ) {
			var nextIndex = Math.floor(Math.random()*list.length);
			while( historyManager.isin( nextIndex ) )
				nextIndex = Math.floor(Math.random()*list.length);
		}
		load(nextIndex);
	}
}

var changingVolume = false;
var old_mousemove = null;
var old_mouseup = null;
function shuffle(){bShuffle=!bShuffle;if(bShuffle){$('#bShuf').css('background-position','-141px -18px');}else{$('#bShuf').css('background-position','-141px 0px');}}
function seek(evt)
{
	var e = (typeof(evt)=='undefined')?event:evt;
	var offset = $('#progress').offset();
	var total = $('#mediaplayer')[0].duration;
	if( $('#mediaplayer').length > 0 )
		$('#mediaplayer')[0].currentTime = parseFloat(e.x - offset.left)/barWidth * total;
}
function changeVolume(evt)
{
	old_mousemove = window.onmousemove;
	changingVolume = true;
	changeVolumeMoving(evt);
	$(window).mousemove(changeVolumeMoving);
}
function changeVolumeMoving(evt)
{
	if( changingVolume ) {
		var e = (typeof(evt)=='undefined')?event:evt;
	//	var offset = $('#volume').offset();
		var volume = parseFloat(e.offsetX)/barWidth;
		if( volume < 0 )
			volume = 0;
		else if( volume > 1.0 )
			volume = 1.0;
		changeVolumeTo(volume);
		old_mouseup = window.onmouseup;
		$(window).mouseup(changeVolumeFinalize);
	}
	if( old_mousemove )
		old_mousemove(evt);
}
function changeVolumeTo(vol)
{
	$('#volume > .on').css('width', (barWidth * vol) + 'px');
	volume = vol;
	$('#mediaplayer')[0].volume = 0.1;
	$('#mediaplayer')[0].volume = vol;
}
function changeVolumeFinalize(e)
{
	changingVolume = false;

	if( old_mouseup )
		old_mouseup(e);
	//$('#volume').unbind('mousemove').bind('mousedown',function(evt){changeVolume(evt);});
}
function loop()
{
	var bLoop = $('#mediaplayer').attr('loop');
	var oPlayer = $('#mediaplayer');
	if( bLoop )
	{
		oPlayer.removeAttr('loop');
		$('#bLoop').css('background-position','-114px 0px');
	}
	else
	{
		oPlayer.attr('loop','loop');
		$('#bLoop').css('background-position','-114px -18px');
	}
}

function buildingList(xml)
{
	list = new Array();
	var index = 0;
	$('#playlist').html('');
	$(xml).find('track').each(function(){
		$('#playlist').append(
			$('<li>').append(
				$('<a>').html($(this).find('title').text())
					.attr('href','javascript:load('+index+')')
					.css('pointer','cursor')
				).data('index', index)
				.bind('dblclick', function(){del($(this).data('index'));prev();})
				.css('cursor','pointer')
			);
		list.push({title:$(this).find('title').text(),loc:$(this).find('location').text()});
		index++;
	});
	repaintList();
//	historyManager.modify(list.length);
	historyManager = new History(list.length, historyFactor);
}

function repaintList()
{
	$('#playlist > li').removeClass('even');
	$('#playlist > li:even').addClass('even');
}

function load( index )
{
	var index = index || 0;
	$('li.playing').removeClass('playing');
	if( list && list.length > 0 )
	{
		moveScroll( index );
		$('#mediaplayer').attr('src',list[index].loc);
		$('#mediaplayer')[0].load();
		$('#playing_name').html("<marquee>" + list[index].title + "</marquee>");//.click(moveScroll);
		play();
		$('#mediaplayer')[0].addEventListener("ended", next, false);

		var w = window;
		while( w.parent.window != w ) {
			w = w.parent.window;
		}
		$(w.document).find('title').text(list[index].title);
		currentIndex = index;
	}
	historyManager.push(currentIndex);
}

function moveScroll( index ) {
	if( typeof( index ) != 'number' )
		index = currentIndex;
	var i = 0;
	$('#playlist > li').each(function(){if($(this).data('index')>=index){return false;}i++;});
	index = i;
	var scroll = $('ul').scrollTop() + $('#playlist > li').eq(index).addClass('playing').position().top - $('#playlist').offset().top - $('#playlist').height()/2;
	$('ul').animate({'scrollTop':scroll},500);
}

function add( title, filepath )
{
	var index = list.length;
	list.push({'title':title,'loc':filepath});
	var lastIndex = $('#playlist > li').length?$('#playlist > li').last().data('index'):-1;
	$('#playlist').append(
			$('<li>').append($('<a>').html(title).attr('href','javascript:load('+index+')').css('pointer','cursor'))
				.data('index',lastIndex+1)
				.bind('dblclick', function(){del($(this).data('index'));prev();})
				.css('cursor','pointer')
		);
	repaintList();
	historyManager.modify(list.length);
}

function del( index )
{
	$('#playlist > li').each(function(){
        if($(this).data('index')==index)
            $(this).detach();
        else if($(this).data('index')>index) {
            $(this).data('index', $(this).data('index')-1)
                .find('a').attr('href', 'javascript:load('+$(this).data('index')+')');
        }
        repaintList();
    });
	list.splice(index,1); // erase
	historyManager.del(index);
	historyManager.modify(list.length);
}

function save()
{
    return $.post(
        'save.php',
        {'json':JSON.stringify(list)},
        function(data){
            if( data ) alert(data);
        },'text'
    );
}

