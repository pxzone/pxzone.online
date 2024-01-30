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
            <div class="first-section padding-bottom-70 c-dwhite">
                <div class="container">
                    <div class="text-left">
                        <!-- <div class="alert text-danger bg-dark">
                            <i class="mdi mdi-exclamation-thick"></i> Not working yet though...
                        </div> -->
                        <h1 class="f-h1 mb-2 c-white">Bitcoin Fee Estimator</h1>
                        <p class="p-text c-white font-15">
                        The fee estimator takes into account the current network conditions and calculates the fee required to have your transaction included in the next block.
                        </p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mt-sm-2 mt-2 mb-3">
                        <div class="col-lg-12">
                            <div class="card card-anchor bg-dark pt-2 pb-2 padding-right-30 padding-left-30">
                                <div class="mt-2 mb-2 text-center">
                                    <label for="" class="text-uppercase">Real Time Transaction Fees</label>
                                    <div class="row card-fees c-white text-center bg-primary-gradient br-5 font-13">
                                        <div class="col-lg-4 col-4">
                                            <div class=" pt-2 pb-2 mt-2">
                                                <label class="c-white ">Low Piority</label><br>
                                                <span id="low_prio_fee"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-4">
                                            <div class=" pt-2 pb-2 mt-2">
                                                <label class="c-white ">Medium Piority</label><br>
                                                <span id="med_prio_fee"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-4">
                                            <div class=" pt-2 pb-2 mt-2">
                                                <label class="c-white">High Piority</label><br>
                                                <span id="high_prio_fee"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 mb-3">
                                <h2>What are Bitcoin Transaction Fees?</h2>
                                <p>A Bitcoin transaction fee is a small fee charged by the network to process a transaction. The fee is paid to the miner who confirms the transaction and adds it to the blockchain. The purpose of transaction fees is to incentivize miners to prioritize and include the transaction in a block, as there is a limited amount of space in each block. If a transaction does not include a fee, or includes a low fee, it may take a long time for the transaction to be confirmed, or it may not be confirmed at all.</p>
                                <h2>Why are Transaction Fees Important?</h2>
                                <p>Transaction fees are an important aspect of using Bitcoin, as they ensure that the network can continue to operate smoothly and securely. The fees also help to prioritize which transactions are confirmed first, ensuring that the network can process high-priority transactions quickly. By paying a higher fee, you can ensure that your transaction is processed quickly and securely.</p>
                                <h2>How to Use a Bitcoin Transaction Fee Estimator?</h2>
                                <p>A Bitcoin transaction fee estimator is a tool that calculates the fee required to have your transaction confirmed in a timely manner. The fee estimator takes into account the current network conditions and calculates the fee required to have your transaction included in the next block.</p>
                                <h2>Conclusion</h2>
                                <p>In conclusion, understanding and using Bitcoin transaction fees is an important aspect of using this digital currency. By using a fee estimator, you can determine the right fee for your transaction and ensure that it is processed quickly and securely. Whether you are using Bitcoin for investment or payment purposes, it is important to have a good understanding of transaction fees and how to use a fee estimator to get the best results.</p>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <!-- End Container -->