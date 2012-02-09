$(document).ready(function() {
	//-- Add Server methods below

	//-- Add Server
	$('#add-server').live('submit', function() {
		var data = {
			name: $('#srv_name').val(),
			host: $('#srv_host').val(),
			clown: $('#srv_clown').val(),
			joke: $('#srv_joke').val(),
			initial_path: $('#srv_initial_path').val(),
			position: $('#srv_position').val(),
			status: $('#srv_status').val(),
			action: 'add-server'
		};

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					var tableHTML = '<tr data-server="' + response.data + '">' +
					'	<td>' + response.data + '</td>' +
					'	<td>' + data.name + '</td>' +
					'	<td>' + data.host + '</td>' +
					'	<td>' + data.initial_path + '</td>' +
					'	<td>' + data.position + '</td>' +
					'	<td>' + (data.status == 1 ? 'Active' : 'Inactive') + '</td>' +
					'	<td class="actions"><a href="#" rel="edit-server" data-server="' + response.data + '" title="Edit"><i class="icon-edit"></i></a> <a href="#" rel="delete-server" data-server="' + response.data + '" title="Delete"><i class="icon-remove"></i></a></td>' +
					'</tr>';

					$('#admin-server-listing').append(tableHTML);
					$('#add-server button[type="reset"]').click();
					showAlert('Server successfully added!', 'success');
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');

		return false;
	});

	//-- Edit Server methods below

	//-- Edit Server
	$('#admin-server-listing a[rel="edit-server"]').live('click', function() {
		var data = {
			server: $(this).attr('data-server'),
			action: 'get-server'
		};

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					$('#srvu_server').val(response.data.id);
					$('#srvu_name').val(response.data.name);
					$('#srvu_host').val(response.data.host);
					$('#srvu_clown').val(response.data.clown);
					$('#srvu_joke').val(response.data.joke);
					$('#srvu_initial_path').val(response.data.initial_path);
					$('#srvu_position').val(response.data.position);
					$('#srvu_status').val(response.data.status);

					$('#edit-server').slideDown();
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');

		return false;
	});

	//-- Update Server
	$('#update-server').live('submit', function() {
		var data = {
			server: $('#srvu_server').val(),
			name: $('#srvu_name').val(),
			host: $('#srvu_host').val(),
			clown: $('#srvu_clown').val(),
			joke: $('#srvu_joke').val(),
			initial_path: $('#srvu_initial_path').val(),
			position: $('#srvu_position').val(),
			status: $('#srvu_status').val(),
			action: 'update-server'
		};

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					$('#admin-server-listing tr[data-server="' + data.server + '"] td:eq(1)').html(data.name);
					$('#admin-server-listing tr[data-server="' + data.server + '"] td:eq(2)').html(data.host);
					$('#admin-server-listing tr[data-server="' + data.server + '"] td:eq(3)').html(data.initial_path);
					$('#admin-server-listing tr[data-server="' + data.server + '"] td:eq(4)').html(data.position);
					$('#admin-server-listing tr[data-server="' + data.server + '"] td:eq(5)').html(data.status == 1 ? 'Active' : 'Inactive');

					$('#update-server button[type="reset"]').click();
					showAlert('Server successfully updated!', 'success');
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');

		return false;
	});

	$('#update-server button[type="reset"]').live('click', function() {
		$('#edit-server').slideUp();
	});

	//-- Delete Server methods below

	var currentServerToDelete = false;

	//-- Delete Server
	$('#admin-server-listing a[rel="delete-server"]').live('click', function() {
		$('#confirm-delete-server').modal('show');

		currentServerToDelete = $(this).attr('data-server');
	});

	//-- Confirm Delete Server
	$('#confirm-delete-server a[rel="confirm-delete-server"]').live('click', function() {
		if (!currentServerToDelete) return false;

		var data = {
			server: currentServerToDelete,
			action: 'delete-server'
		};

		$.post(ajaxurl, data, function(response) {
			if (response) {
				if (response.error) {
					showAlert(response.error, 'error');
				} else if (response.data) {
					$('#admin-server-listing tr[data-server="' + currentServerToDelete + '"]').remove();
					$('#confirm-delete-server').modal('hide');
					showAlert('Server successfully removed!', 'success');
				}
			} else {
				showAlert('Something bad happened...', 'error');
			}
		},'json');
	});

	//-- Cancel Confirm Delete Server
	$('#confirm-delete-server a[rel="cancel-confirm-delete-server"]').live('click', function() {
		$('#confirm-delete-server').modal('hide');
	});

	$('#confirm-delete-server').on('hide', function () {
		currentServerToDelete = false;
	});
});