
function selectCrypto(coin){
	$("#wallet_balance_dd").attr('data-coin', coin);
	$("#wallet_balance_dd").html('<img src="'+base_url+'assets/images/crypto/'+coin+'.webp" class="me-1" height="20" alt="'+coin+'" /> ' + coin.toUpperCase() + "&nbsp;&nbsp;");
}
$("#check_balance").on('click', () => {
	wallet_address = $("#wallet_address").val();
	coin = $("#wallet_balance_dd").data('coin');

	$("#check_balance").text('Checking.....').attr('disabled','disabled');
    let params = new URLSearchParams({'wallet_address':wallet_address, 'coin':coin});
	fetch(base_url+'api/v1/crypto/_get_wallet_balance?' + params, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
		$(".wallet-balance-wrapper").removeAttr('hidden','hidden');
		$("#wallet_balance").html(res.data.balance+ " " +res.data.ticker);
		$("#usd_balance").html("$" +res.data.usd_value);
		$("#eur_balance").html("€" +res.data.eur_value);
        $("#check_balance").text('Show balance').removeAttr('disabled','disabled');
	})
	.catch((error) => {
		console.error('Error:', error);
		$(".wallet-balance-wrapper").attr('hidden','hidden');
        $("#check_balance").text('Show balance').removeAttr('disabled','disabled');
	});
})
