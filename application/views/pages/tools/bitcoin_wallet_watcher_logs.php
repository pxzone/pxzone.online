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
            "item":{
            "@id":"<?=base_url('#tools')?>",
            "name":"Tools"
            }
        },
        {
            "@type":"ListItem",
            "position":3,
            "item":{
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
                        <h1 class="f-h1 mb-2 c-white">Bitcoin Wallet Notifier Logs</h1>
                        <p class="p-text c-white font-15">The list of incoming and outgoing notifications you have received. You can delete this information on our database if you don't want to receive any notification anymore by clicking the "Delete My Record" button.</p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30 table-responsive">
                                <div class="mt-2 mb-2">
                                    <div class="mt-2">
                                        <label for="" class="font-16 fw-700" ><b>Wallet Address:</b> </label>  <span id="_wallet_address"></span>
                                    </div>
                                    <div class="mt-2">
                                        <label for="" class="font-16 fw-700"><b>Email Address:</b> </label> <span id="_email_address"></span>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <table class="table">
                                        <thead>
                                            <th>TXID</th>
                                            <th>BTC Value</th>
                                            <th>USD Value</th>
                                            <th>TX Date</th>
                                        </thead>
                                        <tbody id="logs_tbl"></tbody>
                                    </table>
                                    <div class="row mb-4">
                                        <div class="col-lg-6">
                                            <div class="mt-2  text-start" id="_logs_pagination"></div>
                                           </div>
                                        <div class="col-lg-6">
                                            <div class="mt-2 text-end" id="_log_count"></div>
                                        </div>
                                    </div>
                               </div>
                               <div class="mt-3">
                                    <p>If you wish not to receive anymore notification, you can remove  your records to our database by clicking the button below.</p>
                                    <button class="btn btn-danger rounded btn-lg" id="_del_my_record_btn">Delete My Record!</button>
                               </div>
                               <input type="hidden" id="_global_csrf" name="<?=$csrf_data['name']?>" value="<?=$csrf_data['hash']?>">
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <!-- End Container -->