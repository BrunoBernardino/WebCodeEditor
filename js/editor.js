var openFileChanges = new Array();
var linearKeyCodes = false;

$(document).ready(function() {
	$(window).resize(function() {
		adjustElementSizes();
	});
	adjustElementSizes();

	$(document).live('keydown', function(e) {
		checkForSaveShortcut(e);
	});

	//-- Methods, Events, Triggers, etc.

	//-- List Directory
	$('#server-list a[rel="list-directory"]').live('click', function() {
		var data = {
			server: $(this).attr('data-server'),
			path: $(this).attr('data-path'),
			action: 'list-directory'
		};

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					$(this).addClass('opened').removeClass('closed');
					$('#server-list ul[data-server="' + data.server + '"][data-path="' + escapePath(data.path) + '"]').empty();
					for (var i=0;i<response.data.length;i++) {
						if (response.data[i].dataType == 'file') {
							$('#server-list ul[data-server="' + data.server + '"][data-path="' + escapePath(data.path) + '"]').append('<li><a href="#" rel="open-file" data-server="' + data.server + '" data-file="' + response.data[i].filePath + '" class="closed"><i class="icon-file"></i> ' + response.data[i].fileName + '</a></li>');
						} else {
							$('#server-list ul[data-server="' + data.server + '"][data-path="' + escapePath(data.path) + '"]').append('<li><a href="#" rel="list-directory" data-server="' + data.server + '" data-path="' + response.data[i].filePath + '" class="closed"><i class="icon-play-circle"></i> ' + response.data[i].fileName + '</a> <ul data-server="' + data.server + '" data-path="' + response.data[i].filePath + '" class="nav nav-list"></ul></li>');
						}
					}
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');
	});

	//-- Open File
	$('#server-list a[rel="open-file"]').live('click', function() {
		var data = {
			server: $(this).attr('data-server'),
			file: $(this).attr('data-file'),
			action: 'open-file'
		};

		var originalElement = this;

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					$(originalElement).addClass('opened').removeClass('closed');
					openFileForEditing(data.server, data.file, response.data.contents, $(originalElement).text(), response.data.editMode);
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');
	});

	//-- Close opened file
	$('#files-open a i.icon-remove').live('click', function() {
		var theServer = $(this).closest('a').attr('data-server');
		var theFile = $(this).closest('a').attr('data-file');
		var thePaneID = $(this).closest('a').attr('href');

		//-- Check if there are changes on the file and warn if so
		if (wasFileChanged(theServer, theFile)) {
			showAlert('This file was changed, if you really want to close it, click the close button again, without editing the file.');
			removeFileChange(theServer, theFile);
			return false;
		}

		$(this).closest('li').remove();
		$(thePaneID).remove();

		//-- If we closed the active file, switch to the first one
		if (!$('#files-open li.active').length) {
			if ($('#files-open li:eq(0) a').length) {
				$('#files-open li:eq(0) a').click();
			}
		}
	});

	//-- Save opened file
	$('#files-editor .file-editor .save-file').live('click', function() {
		if ($(this).hasClass('btn-disabled')) {
			return false;
		}

		var data = {
			server: $(this).closest('div').attr('data-server'),
			file: $(this).closest('div').attr('data-file'),
			contents: $(this).closest('div').find('textarea').val(),
			action: 'save-file'
		};

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					showAlert('File successfully saved!', 'success');
					removeFileChange(data.server, data.file);
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');
	});
});

function openFileForEditing(server, filePath, fileContents, fileName, editMode) {
	//-- Check if the file is already open
	if ($('#files-open a[data-server="' + server + '"][data-file="' + escapePath(filePath) + '"]').length) {
		showAlert('The file is already open. Close it and open it again.', 'error');
		return false;
	}

	//-- Add the file in navigation
	$('#files-open').append('<li><a href="#' + $.sha1('f-' + server + '-' + filePath) + '" data-server="' + server + '" data-file="' + filePath + '" data-toggle="tab" title="' + filePath + '">' + fileName + ' <i class="icon-remove"></i></a></li>')

	//-- Add the file to the editor area
	$('#files-editor').append('<div class="tab-pane file-editor" id="' + $.sha1('f-' + server + '-' + filePath) + '" data-server="' + server + '" data-file="' + filePath + '"><textarea id="' + $.sha1('text-' + server + '-' + filePath) + '"></textarea> <br /><p><a href="#" class="btn btn-success save-file"><i class="icon-ok-circle icon-white"></i> Save File</a></p></div>');
	$('#' + $.sha1('f-' + server + '-' + filePath) + ' textarea').val(fileContents);

	//-- Show the opened file
	$('#files-open a[data-server="' + server + '"][data-file="' + escapePath(filePath) + '"]').click();

	//-- Syntax highlighting
	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById($.sha1('text-' + server + '-' + filePath)), {
		mode: editMode,
		indentUnit: 4,
		indentWithTabs: true,
		lineNumbers: true,
		fixedGutter: true,
		onChange: function() {
			var textAreaObject = myCodeMirror.getTextArea();
			addFileChange($(textAreaObject).closest('div').attr('data-server'), $(textAreaObject).closest('div').attr('data-file'));
			myCodeMirror.save();
		},
/*		onKeyEvent: function(codeMirrorEditor, keyEvent) {
			if (keyEvent.type === "keydown") {
				checkForSaveShortcut(keyEvent);
			}
		}*/
	});

	addFileToChangeListener(server, filePath);
	adjustElementSizes();
}

//-- Helpers below

function checkForSaveShortcut(e) {
	if (e.keyCode == 17) {//-- CTRL
		linearKeyCodes = true;
	} else if (linearKeyCodes && e.keyCode == 83) {//-- S
		if ($('#files-editor .file-editor.active .save-file').length) {
			$('#files-editor .file-editor.active .save-file').click();
			linearKeyCodes = false;
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
		linearKeyCodes = false;
	} else {
		linearKeyCodes = false;
	}
}

function addFileToChangeListener(theServer, theFile) {
	removeFileChange(theServer, theFile);

	//-- Trigger change notification when editing file
	//$('#' + $.sha1('f-' + theServer + '-' + theFile) + ' textarea').change(function() {
	//	addFileChange(theServer, theFile);
	//});
}

function addFileChange(theServer, theFile) {
	openFileChanges[$.sha1(theServer + theFile)] = true;
	$('#' + $.sha1('f-' + theServer + '-' + theFile) + ' .save-file').removeClass('btn-disabled');
}

function removeFileChange(theServer, theFile) {
	openFileChanges[$.sha1(theServer + theFile)] = false;
	$('#' + $.sha1('f-' + theServer + '-' + theFile) + ' .save-file').addClass('btn-disabled');
}

function wasFileChanged(theServer, theFile) {
	return openFileChanges[$.sha1(theServer + theFile)];
}

function escapePath(thePath) {
	newPath = thePath.replace(/\//g, "\\/");
	newPath = newPath.replace(/\./g, "\\.");
	return newPath;
}

function adjustElementSizes() {
	$('body').height($(window).height()-10);
	//$('#files-editor textarea').height($('body').height()-200);
	$('#files-editor .CodeMirror, , #files-editor .CodeMirror .CodeMirror-scroll.cm-s-default').height($('body').height()-130);
}