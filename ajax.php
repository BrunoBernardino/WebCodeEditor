<?php
include 'inc/functions.php';

$return = new stdClass();
$return->error = "Something's up...";
$return->data = '';

switch ($_POST['action']) {
	case 'list-directory':
		$serverID = (int) $_POST['server'];
		$serverPath = $_POST['path'];

		$return->error = false;
		$return->data = Helper::getDirectoryListing($serverID, $serverPath);

		if (empty($return->data)) {
			$return->error = "Sorry, I wasn't able to get that listing for you...";
		}
	break;
	case 'open-file':
		$serverID = (int) $_POST['server'];
		$serverFile = $_POST['file'];

		$return->error = false;
		$return->data = new stdClass();
		$return->data->contents = Helper::getServerFile($serverID, $serverFile);
		$return->data->editMode = 'htmlmixed';

		$fileExtension = strtolower(substr(strrchr($serverFile,'.'),1));

		switch ($fileExtension) {
			case 'js':
			case 'json':
				$return->data->editMode = 'javascript';
			break;
			case 'css':
			case 'responsive':
				$return->data->editMode = 'css';
			break;
			case 'xml':
				$return->data->editMode = 'xml';
			break;
			case 'php':
			case 'inc':
				$return->data->editMode = 'php';
			break;
			case 'sql':
				$return->data->editMode = 'mysql';
			break;
		}

		if (empty($return->data->contents)) {
			$return->error = "Sorry, I wasn't able to get that file for you...";
		}
	break;
	case 'save-file':
		$serverID = (int) $_POST['server'];
		$serverFile = $_POST['file'];
		$fileContents = $_POST['contents'];

		$return->error = false;
		$return->data = Helper::saveServerFile($serverID, $serverFile, $fileContents);

		if (empty($return->data)) {
			$return->error = "Sorry, I wasn't able to save that file for you...";
		}
	break;
	//-- Admin functions
	case 'get-server':
		$serverID = (int) $_POST['server'];

		$return->error = false;
		$return->data = Helper::getServerForEdition($serverID);

		if (empty($return->data)) {
			$return->error = "Sorry, I wasn't able to get that server for you...";
		}
	break;
	case 'add-server':
		$serverData = array(
			'name' => $_POST['name'],
			'host' => $_POST['host'],
			'clown' => Helper::encrypt($_POST['clown']),
			'joke' => Helper::encrypt($_POST['joke']),
			'initial_path' => $_POST['initial_path'],
			'position' => (int) $_POST['position'],
			'status' => (int) $_POST['status']
		);

		$return->error = false;
		$return->data = Helper::addServer($serverData);

		if (empty($return->data)) {
			$return->error = "Sorry, I wasn't able to add that server for you...";
		}
	break;
	case 'update-server':
		$serverID = (int) $_POST['server'];
		$serverData = array(
			'name' => $_POST['name'],
			'host' => $_POST['host'],
			'clown' => Helper::encrypt($_POST['clown']),
			'joke' => Helper::encrypt($_POST['joke']),
			'initial_path' => $_POST['initial_path'],
			'position' => (int) $_POST['position'],
			'status' => (int) $_POST['status']
		);

		$return->error = false;
		$return->data = Helper::updateServer($serverID, $serverData);

		if (empty($return->data)) {
			$return->error = "Sorry, I wasn't able to add that server for you...";
		}
	break;
	case 'delete-server':
		$serverID = (int) $_POST['server'];

		$return->error = false;
		$return->data = Helper::deleteServer($serverID);

		if (empty($return->data)) {
			$return->error = "Sorry, I wasn't able to delete that server for you...";
		}
	break;
}

echo json_encode($return);
DB::end();
?>