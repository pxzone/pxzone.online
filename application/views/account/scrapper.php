<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<script src="<?=base_url('assets/js/jquery-3.6.3.min.js')?>"></script>
<script>
    url = "<?=base_url('api/scrapper/altcoinstalks')?>";
    timer();
    function timer(){
        setTimeout(function(){
            access();
        }, 50000);
    }

    function access(){
        fetch(url, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
        })
        .then(response => response.json())
        .then(res => {
            timer();
            console.log('Accessed.')
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
</script>
</body>
</html>