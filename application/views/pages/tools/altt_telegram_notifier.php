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
                        <h1 class="f-h1 mb-2 c-white">AltcoinsTalks Telegram Notifier</h1>
                        <p class="p-text c-white font-15">
                        Notify when someone mention and quote user's post, track other user's post, track phrases, ignore users, etc.
                        </p>
                    </div>
                </div>
            </div>
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card card-anchor bg-dark pt-2 pb-2 padding-right-30 padding-left-30">
                               
                                <div class="mt-3 mb-3">
                                <img width="180" src="https://pxzone.online/assets/images/other/altt-tg-bot.webp" alt="AltcoinsTalks Telegram Notifier">

                                <h2 class="font-20 mt-3">Telegram bot:</h2>
                                <a href="https://t.me/altt_notifier_bot"> <i class="uil uil-telegram font-20"></i> @altt_notifier_bot</a>

                                <h2 class="font-20 mt-3">Features</h2>
                                <ul>
                                    <li>Mention/quote notification</li>
                                    <li>Track Phrase</li>
                                    <li>Track user posts</li>
                                    <li>Track boards</li>
                                    <li>Track replies in a topic/thread</li>
                                    <li>Ignore users</li>
                                    <li>Karma notification</li>
                                    <li>Stop notification/unsubscribe</li>
                                </ul>

                                <h2 class="font-20 mt-3">Command:</h2>
                                <ul>
                                    <li><b>/start</b> - to start the bot, can be seen on the first visit the bot.</li>
                                    <li><b>/menu</b> - shows unsubscribe and track phrase</li>
                                </ul>
                                
                                <h2 class="font-20">Screenshots</h2>
                                <p class="mt-3"><strong><img src="https://i.ibb.co/YLb4qg4/1000002339.gif" alt="" /></strong></p>
                                
                                <p class="mt-3"><strong><img height="203" src="https://i.ibb.co/ss5k0tj/15856356.png" /></strong></p>
                                <p class="mt-3"><img src="https://pxzone.online/api/telegram/users/_count" alt="" width="260" height="17" /></p>

                                <p class="mt-3">
                                The bot scrapes data per <a href="https://www.altcoinstalks.com/index.php?action=recent" target="_blank">action=recent </a>first and second page in 1-minute interval using cron job.
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