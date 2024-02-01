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
                        <!-- <div class="alert text-danger bg-dark">
                            <i class="mdi mdi-exclamation-thick"></i> Not working yet though...
                        </div> -->
                        <h1 class="f-h1 mb-2 c-white">Bitcoin Wallet Address Notifier</h1>
                        <p class="p-text c-white font-15">Receive a notificaion alert when your bitcoin legacy and segwit wallet address send and receive new <br>transaction with no account registration and free of use.</p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-30 padding-left-30">
                                <form id="_bitcoin_wallet_watcher_form" class="mt-3 mb-2">
                                    <label for="" class="mb-1">Enter your details below.</label>
                                    <input type="hidden" id="_global_csrf" name="<?=$csrf_data['name']?>" value="<?=$csrf_data['hash']?>">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="_address" name="wallet_address" required="" placeholder="Bitcoin Wallet Address">
                                        <label for="_address" class="fw-400">Wallet Address</label>
                                    </div>
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control" id="_email" name="email_address" required="" placeholder="Email Address">
                                        <label for="_email" class="fw-400">Email Address</label>
                                    </div>
                                    
                                    <div class="float-end">
                                        <button class="btn btn-lg btn-info rounded mt-1" type="submit" id="_save_wallet_watcher_btn">Save</button>
                                    </div>
                                </form>

                                <div class="mt-3 mb-3">
                                    <h2>What is Bitcoin Wallet Address Watcher?</h2>
                                    <p>
                                        Bitcoin Wallet Adddress Watcher is a tool for tracking Bitcoin wallet balance and transactions. You can input your wallet address and receive updates on 
                                        your current balance, recent transactions and the current value of your holdings. The tool is user-friendly and constantly updates in real-time, providing
                                        you with a quick and easy way to keep track of your Bitcoin investments. The tool can be accessed from anywhere, and it does not require any personal 
                                        information or account creation, making it a secure and convenient option for anyone looking to monitor their Bitcoin holdings.
                                    </p>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <!-- End Container -->