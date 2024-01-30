$("#_bitcoin_wallet_watcher_form").on('submit', function(e){
    e.preventDefault();
	$("#_save_wallet_watcher_btn").text('Saving....').attr('disabled', 'disabled');
	let formData = new FormData(this);
    $.ajax({
		url: base_url+'api/v1/bitcoin/_save_wallet_watcher',
		type: 'POST',
		data: formData,
		cache       : false,
	    contentType : false,
	    processData : false,
	    statusCode: {
		403: function() {
			_error403();
			}
		}
	})
	.done(function(res) {
		if (res.data.status == 'success') {
			Swal.fire({
			  	icon: 'success',
			  	title: 'Success!',
			 	text: res.data.message,
                allowOutsideClick: false,
			})
            $("#_bitcoin_wallet_watcher_form input").val('');
		}
        else if(res.data.message.email_address){
			Swal.fire({
			  	icon: 'error',
			  	title: 'Error!',
			 	html: res.data.message.email_address,
			})
		}
		else{
			Swal.fire({
			  	icon: 'error',
			  	title: 'Error!',
			 	html: res.data.message,
			})
		}
	    $("#_save_wallet_watcher_btn").text('Save').removeAttr('disabled', 'disabled');
		_csrfNonce();
	})
	.fail(function() {
		_csrfNonce();
	    $("#_save_wallet_watcher_btn").text('Save').removeAttr('disabled', 'disabled');
	})
})
$('#_logs_pagination').on('click','a',function(e){
    e.preventDefault(); 
    var page_no = $(this).attr('data-ci-pagination-page');
    getLogs(unique_id, page_no);
});
const getLogs = (unique_id, page_no) =>  {
    let params = new URLSearchParams({'unique_id':unique_id,'page_no':page_no});
	fetch(base_url+'api/v1/logs/_get_logs?' + params, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
        result = res.result;
		let string ='';
		$("#_log_count").text('Count: '+res.count)
		$('#_logs_pagination').html(res.pagination);
        $("#_wallet_address").text(res.attribute.wallet_address);
        $("#_email_address").text(res.attribute.email_address);
		if (result.length > 0) {
			for(var i = 0; i < result.length; i++){
				string +='<tr>'
					+'<td>'+res.result[i].txid+'</td>'
					+'<td>'+res.result[i].btc_value+' BTC</td>'
					+'<td>'+res.result[i].usd_value+' USD</td>'
					+'<td>'+res.result[i].tx_date+'</td>'
				+'</tr>'
			}
			$('#logs_tbl').html(string);
		}
		else{
			$("#logs_tbl").html("<tr class='text-center'><td colspan='4'>No records found!</td></tr>");
		}
	})
	.catch((error) => {
		console.error('Error:', error);
	});
}
if(_state == 'bitcoin_wallet_notifier_logs'){
    getLogs(unique_id, 1)
}
$("#_del_my_record_btn").on('click', () => {
    Swal.fire({
		title: 'Unsubscribe?',
	 	icon: 'warning',
	 	text: 'Are you sure to unsubscribe? By doing so, you will no longer receive bitcoin transaction notification from us. This also will remove your record on our database.',
		showCancelButton: true,
		confirmButtonText: 'Yes, proceed!',
	}).then((result) => {
	  	if (result.isConfirmed) {
            csrf_token = $("#_global_csrf").val();
            $("#_del_my_record_btn").text('Processing....').attr('disabled', 'disabled');
            $.ajax({
                url: base_url+'api/v1/logs/_delete_record',
                type: 'POST',
                data: {'unique_id':unique_id, 'csrf_token':csrf_token},
                statusCode: {
                403: function() {
                    _error403();
                    }
                }
            })
            .done(function(res) {
                if (res.data.status == 'success') {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: res.data.message,
                        allowOutsideClick: false
                    }).then(function() {
                        window.location = base_url+"tools/bitcoin-wallet-notifier";
                    });
                }
                else{
                    Swal.fire({
                          icon: 'error',
                          title: 'Error!',
                         html: res.data.message,
                    })
                }
                $("#_del_my_record_btn").text('Delete My Record').removeAttr('disabled', 'disabled');
            })
            .fail(function() {
                $("#_del_my_record_btn").text('Delete My Record').removeAttr('disabled', 'disabled');
            })
            _csrfNonce();
	  	} 
	})
})
