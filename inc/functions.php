<?php

new DB();
new Helper();

class Helper {
	public function __construct() {
		define('SALT', 'ChangeThisToWhateverYouWant');//-- Change This
	}

	public static function getServerListing() {
		$servers = self::getServers();
?>
<ul id="server-list" class="nav nav-list">
	<li class="nav-header">Servers <small><a href="/admin.php">&raquo; Go to Back-end</a></small></li>
	<?php foreach ($servers as $server) { ?>
	<li>
		<a href="#" rel="list-directory" data-server="<?php echo $server->id; ?>" data-path="<?php echo $server->initial_path; ?>" class="closed"><i class="icon-book"></i> <?php echo $server->name; ?></a>
		<ul data-server="<?php echo $server->id; ?>" data-path="<?php echo $server->initial_path; ?>" class="nav nav-list">
		</ul>
	</li>
	<?php } ?>
</ul>
<?php
	}

	public static function getAdminServerListing() {
		$servers = self::getAdminServers();
?>
<table class="table table-striped table-bordered" id="admin-server-listing">
	<thead>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Host</th>
		<th>Initial Path</th>
		<th>Position</th>
		<th>Status</th>
		<th class="actions">Actions</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($servers as $server) { ?>
	<tr data-server="<?php echo $server->id; ?>">
		<td><?php echo $server->id; ?></td>
		<td><?php echo $server->name; ?></td>
		<td><?php echo $server->host; ?></td>
		<td><?php echo $server->initial_path; ?></td>
		<td><?php echo $server->position; ?></td>
		<td><?php echo ($server->status == 1 ? 'Active' : 'Inactive'); ?></td>
		<td class="actions"><a href="#" rel="edit-server" data-server="<?php echo $server->id; ?>" title="Edit"><i class="icon-edit"></i></a> <a href="#" rel="delete-server" data-server="<?php echo $server->id; ?>" title="Delete"><i class="icon-remove"></i></a></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<div id="confirm-delete-server" class="modal hide fade">
	<div class="modal-header">
		<a href="#" class="close" data-dismiss="modal">×</a>
		<h3>Delete Server</h3>
	</div>
	<div class="modal-body">
		<p>You are about to delete one server, this procedure is irreversible.</p>
		<p>Are you sure you want to proceed?</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-danger" rel="confirm-delete-server">Yes</a>
		<a href="#" class="btn btn-secondary" rel="cancel-confirm-delete-server">No</a>
	</div>
</div>
<?php
	}

	public static function getAdminServers() {
		$sql = "SELECT * FROM `tbl_servers` ORDER BY `position` ASC";
		return DB::execute($sql);
	}

	public static function getServers() {
		$sql = "SELECT * FROM `tbl_servers` WHERE `status` = 1 ORDER BY `position` ASC";
		return DB::execute($sql);
	}

	public static function getServer($id) {
		$sql = "SELECT * FROM `tbl_servers` WHERE `id` = '". (int) $id ."'";
		return DB::sexecute($sql);
	}

	public static function getServerForEdition($id) {
		$sql = "SELECT * FROM `tbl_servers` WHERE `id` = '". (int) $id ."'";
		$return = DB::sexecute($sql);

		$return->clown = self::decrypt($return->clown);
		$return->joke = self::decrypt($return->joke);

		return $return;
	}

	public static function deleteServer($id) {
		$sql = "DELETE FROM `tbl_servers` WHERE `id` = '". (int) $id ."'";
		return DB::query($sql);
	}

	public static function addServer($data) {
		$sql = DB::build($data, 'tbl_servers', 'insert');
		return DB::queryid($sql);
	}

	public static function updateServer($id, $data) {
		$sql = DB::build($data, 'tbl_servers', 'update', "WHERE `id` = '". (int) $id ."'");
		return DB::query($sql);
	}

	//-- Connect to Server
	private static function connectToServer($id) {
		$server = self::getServer($id);

		$ssh = ssh2_connect($server->host, 22);

		ssh2_auth_password($ssh, self::decrypt($server->clown), self::decrypt($server->joke));

		$sftp = ssh2_sftp($ssh);

		return $sftp;
	}

	//-- Get Server Directory Listing
	public static function getDirectoryListing($server, $path) {
		$sftp = self::connectToServer($server);

		$return = array();

		$basePath = "ssh2.sftp://$sftp".$path;

		if ($handle = opendir($basePath)) {

			while (false !== ($entry = readdir($handle))) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}

				if ($path == '/') {
					$allowedRootDirectories = array('srv', 'var', 'home', 'etc', 'root', 'subversion', 'tmp', 'data');
					if (!in_array($entry, $allowedRootDirectories)) {
						continue;
					}
				}

				$isDirectory = is_dir($basePath.$entry);

				if (!$isDirectory) {
					$notAllowedExtensions = array('png', 'jpg', 'jpeg', 'gif', 'bmp', 'svg', 'ai', 'zip', 'tar', 'gz', 'bz2', 'bzip2', '7z', 'fla', 'swf', 'flv', 'avi', 'mpeg', 'mpg', 'mp3', 'wav', 'mp4', 'abf', 'swp');
					$fileExtension = strtolower(substr(strrchr($entry,'.'),1));
					if (!empty($fileExtension) && in_array($fileExtension, $notAllowedExtensions)) {
						continue;
					}

					//-- Check if file mime type starts with text, otherwise, ignore it
					$finfo = finfo_open(FILEINFO_MIME);
					if (substr(finfo_file($finfo, $basePath.$entry), 0, 4) != 'text') {
						continue;
					}
				}

				$entryObject = new stdClass();
				$entryObject->dataType = $isDirectory ? 'directory' : 'file';//-- file OR directory
				$entryObject->filePath = $path . $entry . ($isDirectory ? '/' : '');
				$entryObject->fileName = basename($entry) . ($isDirectory ? '/' : '');

				$return[] = $entryObject;
			}
			closedir($handle);
		}

		return $return;
	}

	//-- Get Server File
	public static function getServerFile($server, $file) {
		$sftp = self::connectToServer($server);

		$return = file_get_contents("ssh2.sftp://$sftp".$file);

		return $return;
	}

	//-- Save Server File
	public static function saveServerFile($server, $file, $contents) {
		$sftp = self::connectToServer($server);

		$return = file_put_contents("ssh2.sftp://$sftp".$file, $contents);

		if ($return === false) {
			return false;
		} else {
			return true;
		}
	}

	//-- Encrypt string
	public static function encrypt($text) {
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}

	//-- Decrypt string
	public static function decrypt($text) {
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}
}

class DB {
	protected static $host;
	protected static $user;
	protected static $pass;
	protected static $database;
	protected static $db;
	protected static $query;
	
	public function __construct() {
		self::$host = 'localhost';
		self::$database = 'YourDatabase';//-- Change This
		self::$user = 'YourDatabaseUser';//-- Change This
		self::$pass = 'YourDatabaseUserPassword';//-- Change This
		self::$query = array();
		self::start();
	}
	
	protected static function start() {
		self::$db = @mysql_connect(self::$host, self::$user, self::$pass, true) OR die('The website is temporarily unavailable (E#001).');
		@mysql_select_db(self::$database, self::$db) OR die('The website is temporarily unavailable (E#002).');
		$sql = "SET NAMES 'utf8'";
		self::query($sql);
	}
	
	public static function query($sql,$i=0) {
		if (self::$query[$i] = mysql_query($sql,self::$db)) {
			return true;
		} else {
			die(mysql_error(self::$db)."\n\n".$sql);
		}
		return false;
	}
	
	public static function queryid($sql,$i=0) {
		$result = 0;
		if (self::query($sql,$i)) {
			$result = self::lastid();
			return $result;
		} else {
			die(mysql_error(self::$db)."\n\n".$sql);
		}
		return $result;
	}
	
	public static function fetch($i=0) {
		if (self::$query[$i]) {
			if ($result = mysql_fetch_object(self::$query[$i])) {
				return $result;
			} else {
				return false;
			}
		}
		return false;
	}
	
	public static function rows($i=0) {
		$rows = mysql_num_rows(self::$query[$i]);
		return $rows;
	}
	
	protected static function fetch_array($i=0) {
		$results = false;
		if (self::$query[$i]) {
			while($result = mysql_fetch_object(self::$query[$i])) $results[] = $result;
		}
		return $results;
	}
	
	public static function execute($sql,$i=0) {
		$results = false;
		self::query($sql,$i);
		$results = self::fetch_array($i);
		return $results;
	}
	
	public static function sexecute($sql,$i=0) {
		$result = false;
		self::query($sql,$i);
		if (self::$query[$i]) {
			$result = self::fetch($i);
		}
		return $result;
	}
	
	public static function get($sql,$i=0) {
		$result = false;
		$result = self::sexecute($sql,$i);
		if ($result) {
			$vars = get_object_vars($result);
			foreach ($vars as $var) {
				return $var;
			}
		}
		return false;
	}
	
	public static function build($array, $table, $action = 'insert', $extra = '') {
		$sql = "";
		switch ($action) {
			case 'insert' : {
				$sql = "INSERT INTO `".$table."` (";
				$fields = "";
				foreach($array as $name=>$value) {
					$fields .= ",`".$name."`";
				}
				$fields = substr($fields,1);
				$sql .= $fields.") VALUES (";
				$fields = "";
				foreach($array as $name=>$value) {
					$fields .= ",'".self::prepare($value)."'";
				}
				$fields = substr($fields,1);
				$sql .= $fields.") ".$extra.";";
			}break;
			case 'update' : {
				$sql = "UPDATE `".$table."` SET ";
				$fields = "";
				foreach($array as $name=>$value) {
					$fields .= ",`".$name."` = '".self::prepare($value)."'";
				}
				$fields = substr($fields,1);
				$sql .= $fields." ".$extra.";";
			}break;
			case 'select' : {
				$sql = "SELECT `id`";
				$fields = "";
				foreach($array as $name=>$value) {
					$fields .= ",`".$name."`";
				}
				$sql .= $fields." FROM `".$table."` ".$extra.";";
			}break;
		}
		return $sql;
	}

	public static function nextid($table = '',$i=0) {
		$sql = "SHOW TABLE STATUS LIKE '".self::prepare($table)."'";
		$result = self::sexecute($sql,$i);
		$return = $result->Auto_increment;
		return $return;
	}
	
	public static function lastid() {
		return mysql_insert_id(self::$db);
	}
	
	public static function prepare($string) {
		$string = stripslashes($string);
		return mysql_real_escape_string($string,self::$db);
	}
	
	public static function end() {
		@mysql_close(self::$db);
	}
}
?>