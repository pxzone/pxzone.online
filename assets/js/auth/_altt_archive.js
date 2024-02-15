if(current_url.indexOf("topic/") > 0 || current_url.indexOf("post/") >  0){
    $('html, body').animate({
        scrollTop: $(".scroll-here").offset().top
    }, 500);
}
$(".back-to-top").on('click', function(){
    $("html, body").animate({scrollTop: 0}, 400);
})
$("#search_archive_btn").on('click', function(){
    page_no = 1;
    let keyword = $("#keyword").val();
    searchArchives(page_no, keyword);
});
$('#pagination').on('click','a',function(e){
    e.preventDefault(); 
    var page_no = $(this).attr('data-ci-pagination-page');
    let keyword = $("#keyword").val();
    searchArchives(page_no, keyword);
});
function searchArchives(page_no, keyword){
    $("#search_archive_btn").html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>').attr('disabled','disabled');
	let params = new URLSearchParams({'keyword':keyword, 'page_no':page_no});
    fetch(base_url+'api/altt/_search?' + params, {
        cache: 'no-cache',
        method: "GET",
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
    })
    .then(response => response.json())
    .then(res => {
        showArchives(page_no, res.data.result, res.data.pagination);
    })
    .catch((error) => {
        $("#search_archive_btn").html('<i class="uil uil-search search-archive-icon"></i> Search').removeAttr('disabled','disabled');
      console.error('Error:', error);
    });
}
function showArchives(page_no, result, pagination){
    $('#pagination').html(pagination);
    post_content = "";
    if(result.length > 0){
        for(var i = 0; i < result.length; i++){
            post_content += '<div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">'
            +'<label for="font-15" class="mt-3 mb-2">Topic ID: '+result[i].topic_id+'</label>'
            +'<div class="hr"></div>'
            +'<div>'
                +'<div>'
                    +'<h3 class="font-17 fw-700"><a href="https://www.altcoinstalks.com/index.php?msg='+result[i].msg_id+'" target="_blank" rel="noopener nofollow">'+result[i].title+'</a></h3>'
                    +'<small>Posted by: <a href="">'+result[i].username+'</a> | '
                    +'<a href="https://www.altcoinstalks.com/index.php?board='+result[i].board_id+'" target="_blank" rel="noopener nofollow">'+result[i].board_name+'</a> • '
                    +''+result[i].date_posted+'</small>'
                +'</div>'
                +'<div class="hr mt-2"></div>'
                +'<div class="font-14 mt-2">'
                +result[i].post
                +'</div>'
            +'</div>'
        +'</div>'
        }
        
        $("#search_result_wrapper").removeAttr('hidden','hidden');
        $("#search_result_wrapper").html(post_content);
        $('html, body').animate({
            scrollTop: $("#search_result_wrapper").offset().top
        }, 500);
    }
    $("#search_archive_btn").html('<i class="uil uil-search search-archive-icon"></i> Search').removeAttr('disabled','disabled');
}
