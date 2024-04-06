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
                            <h1 class="f-h1 mb-2 c-white">Crypto Balance Checker</h1>
                            <p class="p-text c-white font-15">Choose what cryptocurrency and enter wallet address you want to check its balance both in crypto and in USD.</p>
                        </div>
                    </div>
                </div>
                <div class="container">
                   <div class="col-lg-12">
                        <div id="_btc_bal_checker_div" class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-30 padding-left-30">
                            <label for="" class="mt-3">Enter your wallet address.</label>
                            <input type="hidden" id="_global_csrf" name="<?=$csrf_data['name']?>" value="<?=$csrf_data['hash']?>">
                            <div class="input-group ">
                                <input type="text" class="form-control" id="wallet_address" aria-label="Text input with dropdown button" value="<?=isset($_GET['wallet_address']) ? $_GET['wallet_address'] : ''?>">
                                <div class="input-group-append">
                                    <button id="wallet_balance_dd" class="btn btn-outline-secondary dropdown-toggle wallet-balance-dropdown text-uppercase" data-coin="<?=isset($_GET['coin']) ? $_GET['coin'] : 'btc'?>" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="<?=base_url('assets/images/crypto/')?><?=isset($_GET['coin']) ? $_GET['coin'].'.webp' : 'btc.webp'?>" height="20" class="me-1" alt="BTC">  <?=isset($_GET['coin']) ? $_GET['coin'] : 'BTC'?> &nbsp;&nbsp;
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" id="crypto_currency">
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('btc')"><img src="<?=base_url('assets/images/crypto/btc.webp')?>" height="20" class="me-1" alt="BTC"> BTC</a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('eth')"><img src="<?=base_url('assets/images/crypto/eth.webp')?>" height="20" class="me-1" alt="eth"> ETH</a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('bnb')"><img src="<?=base_url('assets/images/crypto/bnb.webp')?>" height="20" class="me-1" alt="bnb"> BNB</a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('ltc')"><img src="<?=base_url('assets/images/crypto/ltc.webp')?>" height="20" class="me-1" alt="ltc"> LTC</a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('doge')" ><img src="<?=base_url('assets/images/crypto/doge.webp')?>" height="20" class="me-1" alt="doge"> DOGE</a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('tron')"><img src="<?=base_url('assets/images/crypto/tron.webp')?>" height="20" class="me-1" alt="tron"> TRON</a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCrypto('bch')" ><img src="<?=base_url('assets/images/crypto/bch.webp')?>" height="20" class="me-1" alt="bch"> BCH</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-1 mb-1">
                                
                                <div class="float-end">
                                    <button class="btn btn-lg btn-primary rounded text-white mt-1" id="check_balance">Show balance</button>
                                </div>
                            </div>
                            <div class="mt-3 mb-2">
                                <div class="wallet-balance-wrapper" hidden="hidden">
                                    <small>Wallet balance</small><br>
                                    <div class="inline-block">
                                        <span class="font-38 fw-600 me-2" id="wallet_balance">0.00</span> <span class="sub-text me-1 font-17" id="usd_balance"></span> <span class="cc-val-separator me-1">•</span> <span class="sub-text font-17" id="eur_balance"></span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <small class="text-muted">Data provided by <a rel="nofollow" target="_blank" href="https://mempool.space/docs/api/rest">Mempool.space</a>, <a rel="nofollow" target="_blank"  href="https://www.oklink.com/docs/en/#introduction">OkLink</a>, & <a rel="nofollow" target="_blank" href="https://www.coingecko.com/en/api/documentation">Coingecko API</a></small>
                                </div>
                            </div>
                        </div> 

                        <div id="" class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-30 padding-left-30">
                            <h2 class="mt-3">What Crypto Balance Checker?</h2>
                            <p class="mt-2">
                            Crypto Balance Checker is a comprehensive online tool designed to assist cryptocurrency investors and enthusiasts in tracking 
                            and manage their digital asset portfolios. With the exponential growth of the cryptocurrency market and the increasing number 
                            of available digital assets, it has become essential for investors to have efficient means to monitor their holdings across 
                            multiple wallets and exchanges. This tool provides users with a centralized platform to conveniently check the balances of 
                            their cryptocurrency holdings in real time, empowering them to make informed investment decisions. 
                           </p>
                        </div> 
                    </div>
                </div>
            </div> 
        </div>


        <!-- End Container -->