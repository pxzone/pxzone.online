<div id="_web_container" class="dark-theme">
            <div class=" other-section padding-bottom-30 c-dwhite"  >
                <div class="first-section padding-bottom-30 c-dwhite">
                    <div class="container">
                        <div class="text-center">
                            <h1 class="f-h1 mb-2 c-white">AltcoinsTalks Archives</h1>
                            <p class="p-text c-white font-15"></p>
                        </div>
                    </div>
                </div>
                <div class="container">
                   <div class="col-lg-12">
                        <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                            <label for="" class="mt-3">Search...</label>
                            <div class="input-group ">
                                <input type="text" class="form-control" id="keyword" placeholder="">
                                <div class="input-group-append">
                                    <button id="select_category_dd" class="btn btn-outline-secondary dropdown-toggle search-archive-dropdown text-capitalize" data-select="topic" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Topic
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" id="select_category">
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCategory('topic')"> Topic </a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCategory('post')"> Post </a></li>
                                        <li><a class="dropdown-item cursor-pointer" onclick="selectCategory('username')"> Username </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mt-1 mb-1">
                                
                                <div class="float-end">
                                    <button class="btn btn-lg btn-primary rounded text-white mt-1" id="search_archive_btn"><i class="uil uil-search"></i> Search</button>
                                </div>
                            </div>
                            <!-- <div class="search-wrapper">
                                <input type="text" class="form-control altt-search-content" id="keyword" placeholder="Topic ID, msg ID, username, topic title, post content.">
                                <div class="dd-archives-wrapper">
                                    <button id="archive_dd" class="btn btn-outline-secondary dropdown-toggle input-archives-dropdown" data-coin="btc" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Post
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" id="">
                                        <li><a class="dropdown-item cursor-pointer"> Post</a></li>
                                        <li><a class="dropdown-item cursor-pointer"> Topic</a></li>
                                        <li><a class="dropdown-item cursor-pointer"> User</a></li>
                                    </ul>
                                </div>
                                <button class="btn btn-primary search-archives-btn" type="button" id="search_archive_btn">
                                    <i class="uil uil-search search-archive-icon"></i> Search
                                </button>
                            </div> -->
                        </div> 
                    </div>
                    
                    <?php if(stripos(current_url(), "topic/") !== false && !empty($post['post'])) { ?>

                    <div class="col-lg-12 scroll-here">
                        <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                            <label for="font-15" class="mt-3 mb-2">Topic ID: <?=$post['topic_id']?></label>
                            <div class="hr"></div>
                            <div>
                                <div>
                                    <h3 class="font-17 fw-700"><a href="https://www.altcoinstalks.com/index.php?topic=<?=$post['topic_id']?>" target="_blank" rel="noopener nofollow"><?=$post['title']?></a></h3>
                                    <small>Posted by: <a href=""><?=$post['username']?></a> at <a href="https://www.altcoinstalks.com/index.php?board=<?=$post['board_id']?>" target="_blank" rel="noopener nofollow"><?=$post['board_name']?></a> • <?=date('F d, Y H:i:s', strtotime($post['date_posted']))?></small>
                                </div>
                                <div class="hr mt-2"></div>
                                <div class="font-14 mt-2">
                                <?=$post['post']?>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <?php } else if(stripos(current_url(), "post/") !== false && !empty($post['post'])) { ?>

                    <div class="col-lg-12 scroll-here">
                        <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                            <label for="font-15" class="mt-3 mb-2">Msg ID: <?=$post['msg_id']?></label>
                            <div class="hr"></div>
                            <div>
                                <div>
                                    <h3 class="font-17 fw-700"><a href="https://www.altcoinstalks.com/index.php?msg=<?=$post['msg_id']?>" target="_blank" rel="noopener nofollow"><?=$post['title']?></a></h3>
                                    <small>Posted by:  <a href=""><?=$post['username']?></a> at <a href="https://www.altcoinstalks.com/index.php?board=<?=$post['board_id']?>" target="_blank" rel="noopener nofollow"><?=$post['board_name']?></a> • <?=date('F d, Y H:i:s', strtotime($post['date_posted']))?></small>
                                </div>

                                <div class="hr mt-2"></div>

                                <div class="font-14 mt-2">
                                <?=$post['post']?>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <?php } else if(stripos(current_url(), "user/") !== false && !empty($post['post'])) { ?>

                    <div class="col-lg-12 scroll-here">
                        <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                            <label for="font-15" class="mt-3 mb-2">Msg ID: <?=$post['msg_id']?></label>
                            <div class="hr"></div>
                            <div>
                                <div>
                                    <h3 class="font-17 fw-700"><a href="https://www.altcoinstalks.com/index.php?msg=<?=$post['msg_id']?>" target="_blank" rel="noopener nofollow"><?=$post['title']?></a></h3>
                                    <small>Posted by:  <a href=""><?=$post['username']?></a> at <a href="https://www.altcoinstalks.com/index.php?board=<?=$post['board_id']?>" target="_blank" rel="noopener nofollow"><?=$post['board_name']?></a> • <?=date('F d, Y H:i:s', strtotime($post['date_posted']))?></small>
                                </div>

                                <div class="hr mt-2"></div>

                                <div class="font-14 mt-2">
                                <?=$post['post']?>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <?php } ?>

                    <div class="col-lg-12" id="search_result_wrapper" hidden="hidden"></div>
                    <div id="pagination"></div>

                    <div class="col-lg-12 scroll-here">
                        <div class="card card-anchor bg-dark c-white pt-2 pb-3 padding-right-30 padding-left-30">
                            <h2 class="font-18 mt-3">Why forum archives are important?</h2>
                            <p class="mt-1">
                            Forums, as online communities, thrive on user-generated content and the exchange of ideas. Archives play a pivotal role in the maintenance, health, and success of a forum for several reasons.
                            Archives are vital for maintaining the integrity, history, and usability of a forum, ensuring it remains a valuable resource and community hub for its users.
                            </p>
                        </div> 
                    </div>

                    <div class="back-to-top cursor-pointer"><i class="uil uil-angle-up"></i></div>
                </div>


            </div> 
        </div>