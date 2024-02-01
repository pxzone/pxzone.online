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
                        <h1 class="f-h1 mb-2 c-white">Bitcoin Signed Message Verifier</h1>
                        <p class="p-text c-white font-15">Verify a bitcoin signed message.</p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card card-anchor bg-dark c-white pt-2 pb-2 padding-right-30 padding-left-30">
                                <div id="_message_verifier_div" class="mt-3 mb-2">
                                    <label for="" class="">Enter your Bitcoin address below.</label>
                                    <input type="hidden" id="_global_csrf" name="<?=$csrf_data['name']?>" value="<?=$csrf_data['hash']?>">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control" id="_address" name="wallet_address" required="" placeholder="Wallet Address">
                                        <label for="username" class="fw-400">Wallet Address</label>
                                    </div>
                                    <div class="mb-3">
                                        <textarea name="message" id="_message" class="form-control" cols="10" rows="5" placeholder="Message" required></textarea>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="_signature" name="signature" required="" placeholder="Signature">
                                        <label for="username" class="fw-400">Signature</label>
                                    </div>
                                    
                                    <div class="float-end">
                                        <button class="btn btn-lg btn-success rounded mt-1" id="_verify_message_btn">Verify</button>
                                    </div>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <script src="<?=base_url('assets/js/vendor/bitcoinjs.min.js')?>"></script>

        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bitcoinjs-lib/0.2.0-1/bitcoinjs-min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bitcoinjs-message@2.2.0/index.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bs58check@3.0.1/index.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bech32@2.0.0/dist/index.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/buffer@6.0.3/index.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/create-hash@1.2.0/browser.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/secp256k1@5.0.0/elliptic.min.js"></script>
        <script src="https://raw.githubusercontent.com/bitcoinjs/varuint-bitcoin/master/index.js"></script> -->
        
        <script>

            // randomAddress();
            function randomAddress() {
                var key = Bitcoin.ECKey.makeRandom();
                var PubKey = key.pub.getAddress().toString();
                var PrivKey = key.toWIF();
                // Hit F12 to enter the Chrome Developer Tools, hit ESC to access the console from there; On Mac, use ⌘-⌥ I
                console.log("Public Key: " + PubKey);
                console.log("Private Key: " + PrivKey);
            }
           verify(
                'bc1q3chtkwfgp4q75k3gm82eag9z6n0nz90647hmfz',
                'INMU/7WR7L5CXxUsdQIm1l0v7MBVSX318juKsGd0jAr7A3JId47gNvDBmEm7LSFl0bRKDiPchbi4XVaI935gjyU=',
                'today is the day'
            );
        </script>


        <!-- End Container -->
        