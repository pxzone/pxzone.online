get_search = $("#search").val();
if(current_url.indexOf("topic/") > 0 || current_url.indexOf("post/") >  0){
    $('html, body').animate({
        scrollTop: $(".scroll-here").offset().top
    }, 500);
}
// else if(keyword !== ''){
//     fetchKarmaLogs(1, keyword)
// }
// else if(current_url.indexOf("karma-log") > 0){
//     fetchKarmaLogs(1, '')
// }
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
function refreshKarmaLogs(){
    $("#karma_log_pagination").html();
    if (history.pushState) {
        history.pushState({path:'/altt/karma-log'},"", '/altt/karma-log');
    }
    $("#search").val('');
    $("#select_sort").val('default');
    fetchKarmaLogs(1, '', 'default')
}
$('#karma_log_pagination').on('click','a',function(e){
    e.preventDefault(); 
    var page_no = $(this).attr('data-ci-pagination-page');
    let keyword = $("#search").val();
    let select_sort = $("#select_sort").val();
    fetchKarmaLogs(page_no, keyword, select_sort);
});
$("#search_form").on('submit', function(e){
    e.preventDefault(); 
    page_no = 1;
    let select_sort = $("#select_sort").val();
    let keyword = $("#search").val();
    fetchKarmaLogs(page_no, keyword, select_sort);
    if (history.pushState) {
        history.pushState({path:'/altt/karma-log?search='+keyword},"", '/altt/karma-log?search='+keyword);
    }
});
function fetchKarmaLogs(page_no, keyword, select_sort){
	$("#karma_log_tbl").html("<tr class='text-center'><td colspan='5'>Getting data...</td></tr>");
	let params = new URLSearchParams({'select_sort':select_sort, 'keyword':keyword, 'page_no':page_no});
    fetch(base_url+'api/altt/karma/_get?' + params, {
        cache: 'no-cache',
        method: "GET",
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
    })
    .then(response => response.json())
    .then(res => {
        showLogs(page_no, res.data.result, res.data.pagination, select_sort);
    })
    .catch((error) => {
		$("#karma_log_tbl").html("<tr class='text-center'><td colspan='5'>No records found!</td></tr>");
    });
}
function showLogs(page_no, result, pagination, select_sort){
    $('#karma_log_pagination').html(pagination);
    karma_logs = "";
    if(result.length > 0){
        for(var i = 0; i < result.length; i++){

            if(select_sort == 'default'){
                $("#title_sort").text('Default Sort');
                $("#karma_point").removeAttr('hidden','hidden');
                $("#datetime").removeAttr('hidden', 'hidden');
                karma_logs += '<tr>'
                    +'<td><a href="https://www.altcoinstalks.com/index.php?action=profile;u='+result[i].uid+'" target="_blank" rel="nofollow">'+result[i].username+'</a></td>'
                    +'<td>'+result[i].position+'</td>'
                    +'<td>'+result[i].karma+'</td>'
                    +'<td>'+result[i].total_karma+'</td>'
                    +'<td>'+result[i].created_at+'</td>'
                +'</tr>'
            }
            else if(select_sort == 'highest_karma_all_time'){
                $("#title_sort").text('All-time High Karma Earner');
                $("#karma_point").attr('hidden', 'hidden');
                $("#datetime").attr('hidden', 'hidden');
                karma_logs += '<tr>'
                    +'<td><a href="https://www.altcoinstalks.com/index.php?action=profile;u='+result[i].uid+'" target="_blank" rel="nofollow">'+result[i].username+'</a></td>'
                    +'<td>'+result[i].position+'</td>'
                    +'<td>'+result[i].total_karma+'</td>'
                +'</tr>'
            }
            else if(select_sort == 'highest_karma_today'){
                $("#title_sort").text('Highest Karma Earner (Today)');
                $("#karma_point").removeAttr('hidden', 'hidden');
                $("#datetime").attr('hidden', 'hidden');
                karma_logs += '<tr>'
                    +'<td><a href="https://www.altcoinstalks.com/index.php?action=profile;u='+result[i].uid+'" target="_blank" rel="nofollow">'+result[i].username+'</a></td>'
                    +'<td>'+result[i].position+'</td>'
                    +'<td>'+result[i].karma+'</td>'
                    +'<td>'+result[i].total_karma+'</td>'
                +'</tr>'
            }
            else if(select_sort == 'highest_karma_this_month'){
                month = getMonth();
                $("#title_sort").text('Highest Karma Earner ('+month+')');
                $("#karma_point").removeAttr('hidden', 'hidden');
                $("#datetime").attr('hidden', 'hidden');
                karma_logs += '<tr>'
                    +'<td><a href="https://www.altcoinstalks.com/index.php?action=profile;u='+result[i].uid+'" target="_blank" rel="nofollow">'+result[i].username+'</a></td>'
                    +'<td>'+result[i].position+'</td>'
                    +'<td>'+result[i].karma+'</td>'
                    +'<td>'+result[i].total_karma+'</td>'
                +'</tr>'
            }
        }
        $('#karma_log_tbl').html(karma_logs);
    }
    else{
		$("#karma_log_tbl").html("<tr class='text-center'><td colspan='5'>No records found!</td></tr>");

    }
}
$("#sort_modal_btn").on('click', function(){
    $("#sort_modal").modal('show');
});
$("#sort_btn").on('click', function(){
    let select_sort = $("#select_sort").val();
    let keyword = $("#search").val();
    if (history.pushState) {
        history.pushState({path:'/altt/karma-log?sort='+select_sort},"", '/altt/karma-log?sort='+select_sort);
    }

    page_no = 1;
	let params = new URLSearchParams({'select_sort':select_sort, 'keyword':keyword, 'page_no':page_no});
    $("#sort_btn").html('<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>').attr('disabled','disabled');

    fetch(base_url+'api/altt/karma/_get?' + params, {
        cache: 'no-cache',
        method: "GET",
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
    })
    .then(response => response.json())
    .then(res => {
        showLogs(page_no, res.data.result, res.data.pagination, select_sort);
        $("#sort_modal").modal('hide');
        $("#sort_btn").html('Sort').removeAttr('disabled','disabled');
    })
    .catch((error) => {
      console.error('Error:', error);
      $("#sort_btn").html('Sort').removeAttr('disabled','disabled');
    });
});
function getMonth(){
    const month = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const d = new Date();
    return month[d.getMonth()];
}