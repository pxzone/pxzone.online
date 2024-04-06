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
                            <!-- <p class="p-text c-white font-15">Website status checker to check if the website or service is down.</p> -->
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8">
                            <div id="" class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-30 padding-left-30">
                                <div class="d-flex justify-content-between">
                                    <div class="mt-2 p-2">Current status</div>
                                    <div class="mt-2 p-2" id="current_status">...</div>
                                </div>                   
                                <div class="mt-3 mb-2">
                                    <label for="" class="website_url"><span class="icon-status"></span> <?= str_replace(array('https://','http://'), '', $site_data['website_url'])?></label>
                                    <div class="mt-2 wm-status-wrapper" id="wm_status_wrapper">
                                    <?php if($this->agent->is_mobile) { ?>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <?php } else { ?>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>
                                        <div class="hr-vertical-disabled"></div>

                                    <?php }?>
                                    </div>

                                    <!-- <div class="d-flex d-flex justify-content-between"">
                                        <div class="p-2"> <small class="text-left font-11 c-light-gray" id="uptime_days">15 days ago</small></div>
                                        <div class="p-2 uptime-hr"></div>
                                        <div class="p-2"> <small class="text-right font-11 c-light-gray">Today</small></div>
                                    </div> -->

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
                            <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                                <h3 class="font-20 mt-4">Response Time</h3>
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
                            <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                                <h3 class="font-20 mt-4">Recent Downtime Activity</h3>
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