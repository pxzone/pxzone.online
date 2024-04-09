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
                        <h1 class="f-h1 mb-2 c-white">Donate</h1>
                        <p class="p-text c-white font-15">
                        Show your support. All donations will go towards the server and domain expenses.
                        </p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mb-3">
                        <div class="">
                            <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30 table-responsive">
                                <div class=" pt-4">
                                    <p>
                                        Donate using the wallet address below:
                                    </p>
                                    <div class="row  text-center font-14">
                                        <div class="col-lg-6">
                                            <div class="mt-2 mb-2">
                                                <div class=" ">
                                                    <h4 for="">Bitcoin</h4>
                                                    <img class="bg-white br-10  " src="<?=base_url('assets/images/other/donate_bitcoin.webp')?>" alt="donate_bitcoin" height="190">
                                                    <div>
                                                        <span for="">bc1q00pxz0k04ndxqdvmkr8kj3fwtlntfctlzp37xl</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-lg-6">
                                            <div class="mt-2 mb-2">
                                                <div class=" ">
                                                    <h4 for="">Monero</h4>
                                                    <img class="bg-white br-10 " src="<?=base_url('assets/images/other/donate_monero.webp')?>" alt="donate_monero" height="190">
                                                    <div>
                                                        <span for="">45eoPvxBkZeJ2nSQHGd9VRCeSvdmKcaV35tbjmprKa13UWVgFzArNR1PWNrZ9W4XwME3iJB9gzMKuSqGc2EWR4ZCTX66NAV</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        <div class="col-lg-6">
                                            <div class="mt-2 mb-2">
                                                <div class=" ">
                                                    <h4 for="">USDT (TRC20)</h4>
                                                    <img class="bg-white br-10 " src="<?=base_url('assets/images/other/donate_usdt.webp')?>" alt="donate_usdt" height="190">
                                                    <div>
                                                        <span for="">TWyvoyijQY2mhnpUMY4bmpk3fX8A66KZTX</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-lg-6">
                                            <div class="mt-2 mb-2">
                                                <div class=" ">
                                                    <h4 for="">Ethereum</h4>
                                                    <img class="bg-white br-10 " src="<?=base_url('assets/images/other/donate_ethereum.webp')?>" alt="donate_ethereum" height="190">
                                                    <div>
                                                        <span for="">0x6e212cB02e53c7d53b84277ecC7A923601422a46</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <!-- End Container -->