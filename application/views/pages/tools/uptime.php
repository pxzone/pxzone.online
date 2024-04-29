        <!-- Container -->
        
        <script type='application/ld+json'>
        {
            "@context":"https://schema.org",
            "@type":"BreadcrumbList",
            "itemListElement":[{
                "@type":"ListItem",
                "position":1,
                "item":
                    {
                    "@id":"<?=base_url()?>",
                    "name":"Home"
                    }
                },
                {
                    "@type":"ListItem",
                    "position":2,
                    "item":
                    {
                        "@id":"<?=base_url('/tools')?>",
                        "name":"Tools"
                    }
                },
                {
                    "@type":"ListItem",
                    "position":3,
                    "item":
                    {
                        "@id":"<?=$canonical_url?>",
                        "name":"<?=$title?>"
                    }
                }
            ]
        }
        </script>
        <div id="_web_container" class="dark-theme">
            <div class=" other-section padding-bottom-30 c-dwhite"  >
                <div class="first-section padding-bottom-30 c-dwhite">
                    <div class="container">
                        <div class="text-center">
                            <h1 class="f-h1 mb-2 c-white"> <?= ucwords(str_replace(array('https://','http://'), '', $site_data['website_url']))?> website status</h1>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8">
                            <div id="" class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-20 padding-left-20 ">
                                <div class="d-flex justify-content-between mt-1">
                                    <h2 class="mt-2 text-left font-14">Current status: <span class="font-14" id="current_status">...</span></h2>
                                    <div class="dropdown float-end mt-2">
                                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="uil-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" onclick="sortUptime('30')" class="dropdown-item mobile-view">30 days</a>
                                            <a href="javascript:void(0);" onclick="sortUptime('60')" class="dropdown-item">60 days</a>
                                            <a href="javascript:void(0);" onclick="sortUptime('90')" class="dropdown-item">90 days</a>
                                            <a href="javascript:void(0);" onclick="sortUptime('120')" class="dropdown-item">120 days</a>
                                        </div>
                                    </div>
                                </div>                   
                                <div class="mt-2 mb-2">
                                    <label for="" class="website_url"><span class="icon-status"></span> <?= str_replace(array('https://','http://'), '', $site_data['website_url'])?></label>
                                    <div class="mt-2 wm-status-wrapper" id="wm_status_wrapper">
                                    </div>
                                    <div class="float-left">
                                        <small class="text-left font-11 c-light-gray" id="uptime_days">30 days ago</small>
                                    </div>
                                    <div class="float-right">
                                        <small class=" font-11 c-light-gray">Today</small>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="col-lg-2"></div>
                    </div> 
                    
                    <div class="row mb-2">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8">
                            <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-20 padding-left-20">
                                <h3 class="font-20 mt-2">Response Time</h3>
                                <div dir="ltr">
                                    <canvas id="response_time_chart" class="apex-charts mt-3"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8">
                            <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-20 padding-left-20">
                                <h3 class="font-20 mt-2">Recent Downtime Activity</h3>
                                <div class="mt-2" id="down_time_activity">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                </div> 
            </div> 
        </div>


        <!-- End Container -->