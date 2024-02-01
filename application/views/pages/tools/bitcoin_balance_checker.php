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
                        <h1 class="f-h1 mb-2 c-white">Bitcoin Balance Checker</h1>
                        <p class="p-text c-white font-15">Check your bitcoin wallet address' balance.</p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <input type="hidden" id="_global_csrf" name="<?=$csrf_data['name']?>" value="<?=$csrf_data['hash']?>">
                            <div id="_btc_bal_checker_div" class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-30 padding-left-30">
                                <div class="mt-3 mb-1">
                                    <label for="" class="">Enter your Bitcoin address below.</label>
                                    <textarea name="" id="_wallet_address" class="form-control" cols="10" rows="8" required></textarea>
                                    <div>
                                        <small class="text-muted">Data provided by <a rel="nofollow" target="_blank" href="https://mempool.space/docs/api/rest">Mempool.space</a> & <a rel="nofollow" target="_blank" href="https://www.coingecko.com/en/api/documentation">Coingecko API</a></small>
                                    </div>
                                    <div class="float-end">
                                        <button class="btn btn-lg btn-warning rounded text-white mt-1" id="_check_btc_bal_btn">Submit</button>
                                    </div>
                                </div>

                                <div class="mt-3 table-responsive">
                                    <table class="table">
                                        <thead>
                                            <th>Address</th>
                                            <th>Bitcoin Balance</th>
                                            <th>USD Vaue</th>
                                            <th>EUR Vaue</th>
                                        </thead>
                                        <tbody id="_btc_balance_tbl"></tbody>
                                    </table>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <!-- End Container -->