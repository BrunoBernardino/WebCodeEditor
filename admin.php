<?php
include 'inc/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>BB Code Editor Admin</title>
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
	<script src="js/admin.js"></script>
</head>

<body>
	<div class="container">
		<div id="alert-area">
		</div>
		<div class="row">
			<div class="admin-content span12">
				<h1>Servers <small><a href="/">&laquo; Go Back to Front-end</a></small></h1>
				<?php Helper::getAdminServerListing(); ?>
				<br />
				<div id="edit-server" style="display: none">
					<legend>Edit Server</legend>
					<form class="well form-inline" id="update-server">
						<input id="srvu_server" type="hidden">
						<input id="srvu_name" type="text" class="input-small" placeholder="Name">
						<input id="srvu_host" type="text" placeholder="Host">
						<input id="srvu_clown" type="text" class="input-small" placeholder="Clown">
						<input id="srvu_joke" type="password" placeholder="Joke">
						<input id="srvu_initial_path" type="text" class="input-small" placeholder="Initial Path">
						<input id="srvu_position" type="number" class="input-small" placeholder="Position">
						<select id="srvu_status" class="span1">
							<option value="1">Active</option>
							<option value="0">Inactive</option>
						</select>
						<br /><br />
						<button type="submit" class="btn btn-success">Update Server</button>
						<button type="reset" class="btn">Cancel</button>
					</form>
					<br />
				</div>
				<legend>Add New Server</legend>
				<form class="well form-inline" id="add-server">
					<input id="srv_name" type="text" class="input-small" placeholder="Name">
					<input id="srv_host" type="text" placeholder="Host">
					<input id="srv_clown" type="text" class="input-small" placeholder="Clown">
					<input id="srv_joke" type="password" placeholder="Joke">
					<input id="srv_initial_path" type="text" class="input-small" placeholder="Initial Path">
					<input id="srv_position" type="number" class="input-small" placeholder="Position">
					<select id="srv_status" class="span1">
						<option value="1">Active</option>
						<option value="0">Inactive</option>
					</select>
					<br /><br />
					<button type="submit" class="btn btn-success">Add Server</button>
					<button type="reset" class="btn">Clear</button>
				</form>
			</div>
		</div> <!-- /row -->
	</div> <!-- /container -->
</body>
</html>
<?php
DB::end();
?>