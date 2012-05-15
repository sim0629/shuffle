<!DOCTYPE html>
<html>
<head></head>
<body>
<script type="text/javascript">
var agent = navigator.userAgent.toLowerCase();
if( agent.indexOf('iphone') != -1 || agent.indexOf('ipad') != -1
	|| agent.indexOf('android') != -1 || agent.indexOf('java0') != -1 ) {
	location.href = 'm.php';
} else {
	location.href = 'index.html';
}
</script>
</body>
</html>
