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
                            <h1 class="f-h1 mb-2 c-dwhite">Bitcoin Price to Image Conversion</h1>
                            <p class="p-text c-dwhite font-15">Bitcoin to fiat conversion, wallet address' balance to image, bitcoin price history to image.</p>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row mt-sm-2 mt-2 mb-3">
                        <div class="col-lg-12">
                            <div id="_btc_bal_checker_div" class="card card-anchor bg-dark c-dwhite pt-2 pb-2 padding-right-30 padding-left-30">
                                <div class="mt-3">
                                    <h2>What is Bitcoin Price to Image?</h2>
                                    <p>
                                            Bitcoin Price to Image is a tool that converts the current bitcoin price into a colorful pixel image. 
                                            The tool updates the image in real-time based on the current bitcoin price available in US Dollars, Euro, British Pound, and Philippine Peso which is obtained from a reliable source. 
                                            This tool can be useful for displaying the current bitcoin price on websites, blogs, or social media platforms and other platforms where an image may be more suitable than text-based 
                                            data in a visually appealing manner.
                                        </p>
                                </div>
                                <div class="mt-1 mb-2 font-15">
                                    <h2 class="f-h2">How to use it?</h2>
                                    <p>Segwit and Legacy wallet address are supported when wallet address balance. Background is transparent and color black <code>#000000</code> is the default font color.</p>
                                    <div class="mb-0">
                                        <label for="">Supported Currency:</label>
                                        <code>
                                            ?currency=ticker
                                        </code>
                                        <ul>
                                            <li>USD</li>
                                            <li>EUR</li>
                                            <li>GBP</li>
                                            <li>PHP</li>
                                        </ul>
                                    </div>

                                        <div class="mb-1">
                                            <label for="">Color:</label>
                                            <code>
                                                ?color=hex_code
                                            </code>
                                            <p class="font-15">6-digit hex codes codes not including the hash sign "#"</p>
                                        </div>
                                    <div class="hr"></div>
                                    <div class="mb-4 mt-4">
                                        <h3 class="font-19">Bitcoin Address Balance:</h3>
                                        <p class="font-15">The default value is Bitcoin</p>
                                        <div class="br-10">
<pre class="text-white">
<?=base_url()?>balance/wallet_address
</pre>
                                        <div class="mb-2">
                                            <label for="">Sample:</label><br>
                                            <pre class="text-blue"><?=base_url()?>balance/1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa?color=ffffff</pre>
                                        </div>
                                        <div class="mb-2">
                                            <label for="">Result:</label>
                                            <div class="mt-0">
                                                <img src="<?=base_url()?>balance/1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa?color=ffffff" alt="wallet balance">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hr"></div>
                                    <div class="mb-4 mt-4">
                                        <h3 class="font-19">Bitcoin to Fiat Conversion:</h3>
                                        <p class="font-15">The default value is BTC/USD conversion</p>
<pre class="text-white">
<?=base_url()?>btc/price/bitcoin_value
</pre>      
                                        <div class="mb-2">
                                            <label for="">Sample:</label><br>
                                            <pre class="text-blue"><?=base_url()?>btc/price/0.001?color=ffffff</pre>
                                        </div>
                                        <div class="mb-2">
                                            <label for="">Result:</label>
                                            <div class="mt-0">
                                                <img src="<?=base_url()?>btc/price/0.001?color=ffffff" alt="bitcoin to fiat conversion">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hr"></div>
                                    <div class="mb-4 mt-4">
                                        <h3 class="font-19">Fiat to Bitcoin Conversion:</h3>
                                        <p class="font-15">The default value is USD/BTC conversion</p>
<pre class="text-white">
<?=base_url()?>fiat/btc/fiat_value
</pre>      
                                        <div class="mb-2">
                                            <label for="">Sample:</label><br>
                                            <pre class="text-blue"><?=base_url()?>fiat/btc/2500?color=ffffff</pre>
                                        </div>
                                        <div class="mb-2">
                                            <label for="">Result:</label>
                                            <div class="mt-0">
                                                <img src="<?=base_url()?>fiat/btc/2500?color=ffffff" alt="bitcoin to fiat conversion">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hr"></div>
                                    <div class="mb-4 mt-4">
                                        <h3 class="font-19">Bitcoin Price History:</h3>
                                        <p class="font-15">The default value is USD conversion</p>

<pre class="text-white">
<?=base_url()?>btc/history/dd-mm-yyyy
</pre>
                                        <div class="mb-2">
                                            <label for="">Sample:</label><br>
                                            <pre class="text-blue"><?=base_url()?>btc/history/<?=date('d-m-Y',strtotime('-2 month'))?>?color=ffffff</pre>
                                        </div>
                                        <div class="mb-2">
                                            <label for="">Result:</label>
                                            <div class="mt-0">
                                                <img src="<?=base_url()?>btc/history/<?=date('d-m-Y',strtotime('-2 month'))?>?color=ffffff" alt="bitcoin history value">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <small class="text-muted">Data provided by <a rel="nofollow" target="_blank" href="https://www.blockchain.com/explorer/api">Blockchain.com</a> & <a rel="nofollow" target="_blank" href="https://www.coingecko.com/en/api/documentation">Coingecko API</a></small>
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