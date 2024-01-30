if(_state == 'index'){
    _getArticles(1);
}
function _getArticles(page_no){
	let params = new URLSearchParams({'page_no':page_no});
	fetch(base_url+'api/v1/article/_get?' + params, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
		string = '';
		result = res.data.result;
		$("#_category_title").html('<h2 class="text-light">'+res.data.category+'</h2>');
		for(var i in result){
			string +='<div class="col-lg-4 mb-4">'
                    +'<a href="'+result[i].url+'" class="text-light">'
                        +'<div class="">'
                            +'<img src="'+result[i].article_image+'" class="img-fluid br-10 mb-1" alt="'+result[i].title+'">'
                            +'<span class="text-muted font-14">'+result[i].created_at+'  • '+result[i].min_read+' min read</span>'
                            +'<h2 class="font-20 fw-600" >'+result[i].title+'</h2>'
                        +'</div>'
                   +' </a>'
                +'</div>';
		}
		$("#articles_pagination").html(res.data.pagination)

		$("#articles_data").html(string)
	})
	.catch((error) => {
		console.error('Error:', error);
	});
}
$('#articles_pagination').on('click','a',function(e){
    e.preventDefault(); 
    var page_no = $(this).attr('data-ci-pagination-page');
    _getArticles(page_no);
});