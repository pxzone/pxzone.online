getSiteVisits('7_days');
function getSiteVisits(range){
    let params = new URLSearchParams({'range':range});
    fetch(base_url+'api/v1/statistics/_get_site_visits?' + params, {
        method: "GET",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
    })
    .then(response => response.json())
    .then(res => {
        visit_today = res.data.visit_today;
        site_visit = res.data.site_visit;
        $("#visits_today").text(visit_today)
        siteVisitChart(site_visit);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}
var visit_stat_chart;
function siteVisitChart(site_visit) {
    let click_date = [];
    let click_count = [];
    if (visit_stat_chart) {
        visit_stat_chart.destroy();
    }
    for(var i in site_visit){
        click_date.push(site_visit[i].date);
        click_count.push(site_visit[i].views);
    }
    const ctx = document.getElementById('website_visits');
    visit_stat_chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: click_date,
            datasets: [
                {
                    label: 'Site Visit',
                    data: click_count,
                    fill: true,
                    backgroundColor: 'rgba(5, 203, 98, .09)',
                    borderColor: 'rgba(5, 203, 98, 1)',
                    borderJoinStyle: 'round',
                    borderWidth: 1.5,
                    tension: .3
                }
            ]
        },
        options: {
           
            scales: {
                x: {
                    grid: {
                      display: false,
                    }
                },
                y: {
                    grid: {
                      display: false
                    }
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: 'circle'
                    }

                }
            },
        }
    });
}