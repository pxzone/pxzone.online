var posts_chart;
var topics_chart;

if(current_url.indexOf("altt/") > 0 || current_url.indexOf("") >  0){
    $('html, body').animate({
        scrollTop: $(".scroll-here").offset().top
    }, 500);
}
function getPostsChartStat(){
    fetch(base_url+'api/altt/statistics/_get_posts_chart', {
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        posts = res.data;
        let date = [];
        let post_count = [];
        if (posts_chart) {
            posts_chart.destroy();
        }
        for(var i in posts){
            date.push(posts[i].date);
            post_count.push(posts[i].posts);
        }
        const ctx = document.getElementById('posts_per_day');
        posts_chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: date,
                datasets: [{
                    // label: 'Posts',
                    data: post_count,
                    fill: true,
                    backgroundColor: 'rgba(220, 147, 5, .1)',
                    borderColor: 'rgba(220, 147, 5, 1)',
                    borderJoinStyle: 'round',
                    borderWidth: 1,
                    tension: .1
                }]
            },
            options: {
                scales: {
                    x: {
                        grid: {
                        display: true,
                        }
                    },
                    y: {
                        grid: {
                        display: false
                        }
                    },
                },
                plugins: {
                    legend: false
                },
            }
        });
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
function getTopicsChartStat(){
    fetch(base_url+'api/altt/statistics/_get_topics_chart', {
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        topics = res.data;
        let date = [];
        let topics_count = [];
        if (topics_chart) {
            topics_chart.destroy();
        }
        for(var i in topics){
            date.push(topics[i].date);
            topics_count.push(topics[i].topics);
        }
        const ctx = document.getElementById('topics_per_day');
        topics_chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: date,
                datasets: [{
                    // label: 'Posts',
                    data: topics_count,
                    fill: true,
                    backgroundColor: 'rgba(220, 147, 5, .1)',
                    borderColor: 'rgba(220, 147, 5, 1)',
                    borderJoinStyle: 'round',
                    borderWidth: 1,
                    tension: .1
                }]
            },
            options: {
                scales: {
                    x: {
                        grid: {
                        display: true,
                        }
                    },
                    y: {
                        grid: {
                        display: false
                        }
                    },
                },
                plugins: {
                    legend: false
                },
            }
        });
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
function getBasicStat(){
    fetch(base_url+'api/altt/statistics/_get_basic_stat', {
        mode: 'no-cors',
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        $("#posts_24h").text(res.data.post_24h);
        $("#topics_24h").text(res.data.topic_24h);
        $("#karma_24h").text(res.data.karma_24h);
        $("#archive_posts_all").text(res.data.archive_posts_count);
        $("#archive_topic_all").text(res.data.archive_topics_count);
        $("#parsed_users_all").text(res.data.parsed_users);
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}
function getLatestTopic(){
    fetch(base_url+'api/altt/statistics/_get_latest_topic', {
        mode: 'no-cors',
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        data = res.data;
        latest_topics = "";
        if (data.length > 0) {
            for(var i = 0; i < data.length; i++){
                latest_topics += '<div class=" row">'
                    +'<div class="col-lg-12">'
                        +'<label><a target="_blank" rel="noopener" href="https://www.altcoinstalks.com/index.php?topic='+data[i].topic_id+'">'+data[i].topic_name+'</a></label>'
                    +'</div>'
                +'</div>';
            }
            $("#latest_topics").html(latest_topics);
        }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}
function getTopPosters(){
    fetch(base_url+'api/altt/statistics/_get_top_posters', {
        mode: 'no-cors',
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        data = res.data;
        top_posters = "";
        if (data.length > 0) {
            for(var i = 0; i < data.length; i++){
                top_posters += '<div class=" row">'
                    +'<div class="col-lg-7">'
                        +'<label><a target="_blank" rel="noopener" href="https://www.altcoinstalks.com/index.php?action=profile;u='+data[i].uid+'">'+data[i].username+'</a></label>'
                    +'</div>'
                    +'<div class="col-lg-5">'
                        +'<div class="progress mt-1">'
                            +'<div class="progress-bar bg-warning" role="progressbar" style="width: '+data[i].percent+'%;" aria-valuenow="'+data[i].percent+'" aria-valuemin="0" aria-valuemax="100">'+data[i].post_count+'</div>'
                        +'</div>'
                    +' </div>'
                +'</div>'
            }
            $("#top_posters").html(top_posters);
        }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}
function getMostTopicByReplies(){
    fetch(base_url+'api/altt/statistics/_get_most_topic_replies', {
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        data = res.data;
        top_topics = "";
        if (data.length > 0) {
            for(var i = 0; i < data.length; i++){
                top_topics += '<div class=" row">'
                    +'<div class="col-lg-7">'
                        +'<label><a target="_blank" rel="noopener" href="https://www.altcoinstalks.com/index.php?topic='+data[i].topic_id+'">'+data[i].topic_name+'</a></label>'
                    +'</div>'
                    +'<div class="col-lg-5">'
                        +'<div class="progress mt-1">'
                            +'<div class="progress-bar bg-warning" role="progressbar" style="width: '+data[i].percent+'%;" aria-valuenow="'+data[i].percent+'" aria-valuemin="0" aria-valuemax="100">'+data[i].reply_count+'</div>'
                        +'</div>'
                    +' </div>'
                +'</div>'
            }
            $("#top_topics").html(top_topics);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
function getTopTopicStarter(){
    fetch(base_url+'api/altt/statistics/_get_most_topic_starter', {
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        data = res.data;
        topic_starters = "";
        if (data.length > 0) {
            for(var i = 0; i < data.length; i++){
                topic_starters += '<div class=" row">'
                    +'<div class="col-lg-7">'
                        +'<label><a target="_blank" rel="noopener" href="https://www.altcoinstalks.com/index.php?action=profile;u='+data[i].uid+'">'+data[i].username+'</a></label>'
                    +'</div>'
                    +'<div class="col-lg-5">'
                        +'<div class="progress mt-1">'
                            +'<div class="progress-bar bg-warning" role="progressbar" style="width: '+data[i].percent+'%;" aria-valuenow="'+data[i].percent+'" aria-valuemin="0" aria-valuemax="100">'+data[i].topic_count+'</div>'
                        +'</div>'
                    +' </div>'
                +'</div>'
            }
            $("#most_topic_starter").html(topic_starters);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
function getTopBoards(){
    fetch(base_url+'api/altt/statistics/_get_top_board', {
        method: "GET",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(res => {
        data = res.data;
        top_boards = "";
        if (data.length > 0) {
            for(var i = 0; i < data.length; i++){
                top_boards += '<div class=" row">'
                    +'<div class="col-lg-7">'
                        +'<label><a target="_blank" rel="noopener" href="https://www.altcoinstalks.com/index.php?board='+data[i].board_id+'">'+data[i].board_name+'</a></label>'
                    +'</div>'
                    +'<div class="col-lg-5">'
                        +'<div class="progress mt-1">'
                            +'<div class="progress-bar bg-warning" role="progressbar" style="width: '+data[i].percent+'%;" aria-valuenow="'+data[i].percent+'" aria-valuemin="0" aria-valuemax="100">'+data[i].post_count+'</div>'
                        +'</div>'
                    +' </div>'
                +'</div>'
            }
            $("#top_boards").html(top_boards);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
getBasicStat();
getPostsChartStat();
getTopicsChartStat();
getLatestTopic();
// getTopPosters();
// getTopTopicStarter();
// getMostTopicByReplies();
// getTopBoards();
