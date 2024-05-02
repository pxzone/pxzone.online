function sortUptime(sort_days){
    $("#loader").removeAttr('hidden', 'hidden');
    getUptimeData(site, sort_days);
}
function getUptimeData(site, sort_days){
    str ="";
    div_num = 60;
    if ($(window).width() < 500) {
        div_num = 30;
        
    } 
    for(var n = 0; n < div_num; n++){
        str += '<div class="hr-vertical-disabled"></div>';
    }
    $("#wm_status_wrapper").html(str);


    getUptimeActivty(site, sort_days);
    getUptimeResponseTime(site, sort_days);
	let params = new URLSearchParams({'site':site, 'sort':sort_days});
	fetch(base_url+'api/v1/monitor/_get_data?' + params, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
        string = "";
        string2 = "";
        string3 = "";
        uptime_status = "";
        uptime_color = "";
        uptime_text = "";
        uptime_text2 = "";
        uptime_icon = "";
        span_class = "";

        query_all = res.result.data;
        latest_row = res.result.latest_row;
        row_count = query_all.length;
        default_count = res.result.count;
        diff_count = default_count - row_count;
        $("#uptime_days").text(default_count+' days ago');
        console.log()
        if (latest_row.status == 'up') {
            uptime_icon = "<i class='uil uil-check-circle text-success'></i>";
            uptime_text2 = "Operational";
            span_class = "text-success";
        }
        else if (latest_row.status == 'down')
        {
            uptime_icon = "<i class='uil-exclamation-circle text-danger'></i>";
            uptime_text2 = "Down";
            span_class = "text-danger";
        }
        $("#current_status").html(uptime_icon +' '+ uptime_text2).addClass(span_class)
        $(".icon-status").html(uptime_icon)

        if (query_all.length > 0) {
            for(var i = 0; i < query_all.length; i++){
                if(query_all[i].down_count >=5 ){
                    uptime_color = "hr-vertical-down";
                    uptime_text = "<div class='fw-500'><i class='uil-exclamation-circle text-danger'></i> Downtime</div> <div class='c-light-gray'> Down for "+query_all[i].down_count+" mins </div>";
                }
                else if(query_all[i].down_count < 5 && query_all[i].down_count >= 1 ){
                    uptime_color = "hr-vertical-up";
                    uptime_text = "<div class='fw-500'><i class='uil uil-check-circle text-success'></i>  Operational</div> <div class='c-light-gray'> Down for "+query_all[i].down_count+" mins </div>";
                }
                else{
                    uptime_color = "hr-vertical-up";
                    uptime_text = "<i class='uil uil-check-circle text-success'></i> Operational";
                }

                
                string += '<div class="'+uptime_color+'"><div class="tooltip_down font-12">'+uptime_text+' <div class="hr-hover"></div><span class="c-light-gray">'+query_all[i].date+'</span></div></div>';
            }
            $("#wm_status_wrapper").html(string);
        }
        for(var x = 0; x < diff_count; x++){
            string2 += '<div class="hr-vertical-disabled"></div>';
        }
        $("#wm_status_wrapper").append(string2);
        $("#loader").attr('hidden', 'hidden');
	})
	.catch((error) => {
		console.error('Error:', error);
	});
}
function getUptimeActivty(site, sort_days){
    $("#down_time_activity").html('<div class="text-center mt-2 mb-2 font-12 c-light-gray">Getting records...</div>');
	let params = new URLSearchParams({'site':site, 'sort':sort_days});
	fetch(base_url+'api/v1/monitor/_get_data_activity?' + params, {
  		method: "GET",
		  	headers: {
		    	'Accept': 'application/json',
		    	'Content-Type': 'application/json'
		  	},
	})
	.then(response => response.json())
	.then(res => {
        string = "";
        string2 = "";
        result = res.result.data;
        if(result.length > 0){
            for(var i = 0; i < result.length; i++){
                string += '<div class="downtime-details font-12">'
                string +='<h4>'+result[i].date+'</h4>'
                details = result[i].details;
                string +='<table class="table table-sm table-borderless mb-0">'
                string +='<thead>'
                string +='<tr><th>Time</th><th>Response time</th><th>Status code</th></tr>'
                string +='</thead>'
                string +='<tbody>'
                for(var x in details){
                    // string +='<div class="dta-details"><div>'+details[x].datetime+'</div> <div>'+details[x].response_time+'</div> </div>';
                    string +='<tr>'
                    string +='<td>'+details[x].datetime+'</td><td>'+details[x].response_time+'</td><td><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/'+details[x].status_code+'" target="_blank" rel="noopener nofollow">'+details[x].status_code+'</a></td>'
                    string +='</tr>'
                }
                string +='</tbody>'
                string +='</table>'
               string +='</div>';
            }
            $("#down_time_activity").html(string);
        }
        else{
            $("#down_time_activity").html('<div class="text-center mt-2 mb-2 font-12 c-light-gray">No downtime record found!</div>');
        }

	})
	.catch((error) => {
		console.error('Error:', error);
	});
}
var response_time_chart;
function getUptimeResponseTime(site, sort_days){
    let params = new URLSearchParams({'site':site, 'sort':sort_days});
	fetch(base_url+'api/v1/monitor/_get_response_time?' + params, {
  		method: "GET",
		headers: {
			'Accept': 'application/json',
	    	'Content-Type': 'application/json'
	  	},
	})
	.then(response => response.json())
	.then(res => {
        response = res.result;
        
        let date = [];
        let response_time = [];
       
        if (response_time_chart) {
            response_time_chart.destroy();
        }
        for(var i in response){
            date.push(response[i].date);
            response_time.push(response[i].response_time);
        }
        const ctx = document.getElementById('response_time_chart');
        response_time_chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: date,
                datasets: [{
                    // label: 'Posts',
                    data: response_time,
                    fill: true,
                    backgroundColor: 'rgba(129, 177, 250, .3)',
                    borderColor: 'rgba(14, 100, 233, 1)',
                    borderJoinStyle: 'miter',
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
                        display: true
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
