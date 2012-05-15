<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Shuffle!</title>
	<link rel="stylesheet" href="/jquery-mobile/compiled/jquery.mobile-1.0b3pre.min.css" />
</head>
<body>
	<div data-role="page"></div>
	<script src="/jquery-mobile/js/jquery.js"></script>
	<script src="/jquery-mobile/compiled/jquery.mobile-1.0b3pre.min.js"></script>
	<script>
	function init()
	{
		if( location.hash.length > 0 )
		{
			load(location.hash.substr(1));
		}
		else
		{
			load("");
		}
	}

	function clear()
	{
	}

	function load(dirname)
	{
		dirname = dirname.replace('%F2', '/');
		$.get('api/',
		{
			'action':'list',
			'param_list':'context',
			'context':'D:' + dirname
		},
		function(data){
			data = data.sort(function(a,b){return a.data>b.data?1:-1;});
			_$list = $('<ul>');
			for( var i in data )
			{
				var fClick	= function(i){
					return function(){
						try
						{
							clear();
							var fulldir = ((dirname=="")?"":(dirname + "/")) + data[i].data;
							$.mobile.changePage('#' + fulldir.replace('/', '%F2'));
							load(fulldir);
						}
						catch(e)
						{
							alert('...');
							return;
						}
					};
				};
				_$data = $('<a>').text(data[i].data).click(fClick(i));
				$('<li>').append(_$data).appendTo(_$list);
			}
			$.mobile.activePage.append(_$list);
			_$list.listview();
		},'json');
	}

	$(init);
	</script>
</body>
</html>
