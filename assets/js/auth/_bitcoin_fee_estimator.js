const getFeeEstimates = () =>  {
	fetch(base_url+'api/v1/bitcoin/_get_recommended_fees', {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
       $("#low_prio_fee").text(res.minimumFee+' sat/vB')
       $("#med_prio_fee").text(res.economyFee+' sat/vB')
       $("#high_prio_fee").text(res.fastestFee+' sat/vB')

	})
	.catch((error) => {
		console.error('Error:', error);
	});
}

if(_state == 'bitcoin_fee_estimator'){
    getFeeEstimates();
}