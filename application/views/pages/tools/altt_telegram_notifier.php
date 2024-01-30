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
                        <h1 class="f-h1 mb-2 c-white">AltcoinsTalks Telegram Notifier</h1>
                        <p class="p-text c-white font-15">
                        Notify when someone mention and quote user's post, track other user's post, track phrases, ignore users, etc.
                        </p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mt-sm-2 mt-2 mb-3">
                        <div class="col-lg-12">
                            <div class="card card-anchor bg-dark pt-2 pb-2 padding-right-30 padding-left-30">
                               
                                <div class="mt-3 mb-3">
                                <img width="100" src="https://i.ibb.co/D7n9vQM/Altcoinstalks-Notifier1.png" alt="AltcoinsTalks Telegram Notifier">

                                <h2>Telegram bot:</h2>
                                <a href="https://t.me/altt_notifier_bot">@altt_notifier_bot</a>

                                <h2>Features</h2>
                                <ul>
                                    <li>Mention/quote notification</li>
                                    <li>Track Phrase</li>
                                    <li>Track user posts</li>
                                    <li>Ignore user posts</li>
                                    <li>Stop notification/unsubscribe</li>
                                </ul>

                                <h2>Command:</h2>
                                <ul>
                                    <li><b>/start</b> - to start the bot, can be seen on the first visit the bot.</li>
                                    <li><b>/menu</b> - shows unsubscribe and track phrase</li>
                                </ul>

                                <p class="mt-3">
                                The bot scrapes data per msg_id in 1-minute intervals, meaning the notification might be delayed by 1 minute or more. 
                                The interval is due to the cron job's limit. The delay might be more painful if the site's server is down. 
                                The interval time will be changed in the future so in the meantime..
                                </p>

                                AltcoinsTalks Thread: <a href="https://www.altcoinstalks.com/index.php?topic=315728.0" target="_blank" rel="nopener nofollow">Here...</a>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>



        <!-- End Container -->