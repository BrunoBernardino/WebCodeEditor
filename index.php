<?php
include 'inc/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>BB Code Editor</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Bruno Bernardino">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap.responsive.css" rel="stylesheet">
	<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet">
	<link href="css/global.css" rel="stylesheet">

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/global.js"></script>
	<script src="js/editor.js"></script>
	
	<!-- CodeMirror -->
	<script src="js/codemirror.js"></script>
	<link href="css/codemirror.css" rel="stylesheet">

	<script src="inc/codemirror/javascript.js"></script>
	<script src="inc/codemirror/css.js"></script>
	<script src="inc/codemirror/htmlmixed.js"></script>
	<script src="inc/codemirror/xml.js"></script>
	<script src="inc/codemirror/clike.js"></script>
	<script src="inc/codemirror/php.js"></script>
	<script src="inc/codemirror/mysql.js"></script>
</head>

<body>
	<div class="container-fluid">
		<div id="alert-area">
		</div>
		<div class="row-fluid">
			<div class="sidebar span3">
				<?php Helper::getServerListing(); ?>
			</div>

			<div class="content span10">
				<div class="tabbable">
					<ul class="nav nav-tabs" id="files-open"></ul>
				
					<div class="tab-content" id="files-editor">
					</div>
				</div>
			</div>
		</div> <!-- /row-fluid -->
	</div> <!-- /container-fluid -->
</body>
</html>
<?php
DB::end();
?>