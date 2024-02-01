$("#_check_btc_bal_btn").on('click', () => {
    wallet_address = $("#_wallet_address").val();
    if(!wallet_address){
        Swal.fire({
            icon: 'warning',
            title: 'Ooof!',
            text: 'Bitcoin Wallet Address is required!'
      });
      return false;
    }
    $("#_check_btc_bal_btn").text('Please wait.....').attr('disabled','disabled');
    let params = new URLSearchParams({'wallet_address':wallet_address});
	fetch(base_url+'api/v1/bitcoin/_get_wallet_balance?' + params, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
		let tbl ='';
		if (res.data.length > 0) {
			for(var i = 0; i < res.data.length; i++){
				tbl +='<tr>'
					+'<td>'+wallet_address+'</td>'
					+'<td>'+res.data[i].btc_balance+'</td>'
					+'<td>'+res.data[i].usd_value+'</td>'
					+'<td>'+res.data[i].eur_value+'</td>'
				+'</tr>'
			}
			$('#_btc_balance_tbl').html(tbl);
		}
		else{
			$("#_btc_balance_tbl").html("<tr class='text-center'><td colspan='4'>Invalid wallet address!</td></tr>");
		}
        $("#_check_btc_bal_btn").text('Submit').removeAttr('disabled','disabled');
	})
	.catch((error) => {
		console.error('Error:', error);
        $("#_check_btc_bal_btn").text('Submit').removeAttr('disabled','disabled');
	});
})