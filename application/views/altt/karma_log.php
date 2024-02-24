        <div id="_web_container" class="dark-theme">
            <div class=" other-section padding-bottom-30 c-dwhite"  >
                <div class="first-section padding-bottom-30 c-dwhite">
                    <div class="container">
                        <div class="text-center">
                            <h1 class="f-h1 mb-2 c-white">Altcoins Talks Karma Logs</h1>
                            <p class="p-text c-white font-15"></p>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="col-lg-12">
                        <div class="card card-anchor bg-dark c-white pt-2 pb-5 padding-right-30 padding-left-30">
                            <div class="row mb-2 mt-3">
                                <div class="col-xl-6">
                                    <form class="row gy-2 gx-2 align-items-center justify-content-xl-start justify-content-between" id="search_form">
                                        <div class="col-auto">
                                            <label for="_keyword" class="visually-hidden">Search</label>
                                            <input type="search" class="form-control rounded" style="height:37.6px;" name="search" id="search" placeholder="Search username...">
                                        </div>
                                    </form>                            
                                </div>
                                <div class="col-xl-6">
                                    <div class="text-xl-end mt-xl-0 mt-2">
                                        <button type="button" class="btn rounded btn-secondary mb-2" id="sort_modal_btn"><i class="uil-sort"></i> Sort</button>
                                        <button type="button" class="btn rounded btn-secondary mb-2" onclick="refreshKarmaLogs()"><i class="uil-redo"></i> Refresh</button>
                                    </div>
                                </div> 
                            </div>

                            <div class="mt-2 table-responsive">
                            <h2 class="font-20" id="title_sort">Default Sort</h2>
                            <table class="table table-centered mb-0 font-14">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th id="position">Position</th>
                                        <th id="karma_point">Karma Point</th>
                                        <th id="total_karma">Total Karma</th>
                                        <th id="datetime">Datetime</th>
                                    </tr>
                                </thead>
                                <tbody id="karma_log_tbl">
                                </tbody>
                            </table> 
                            </div>
                        </div> 
                    </div>
                                      
                    <div id="karma_log_pagination"></div>

                    <div class="modal fade margin-top-10" id="sort_modal" data-bs-backdrop="static" data-bs-keyboard="false"   tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content br-10">
                                <div class="modal-body">
                                    <div class="mt-2 ">
                                        <h3 class="text-left text-capitalize text-dark"> <i class="uil-sort"></i> Sort</h3>
                                    </div>
                                    <div class="mt-2">
                                        <select name="select_sort" id="select_sort" class="form-select">
                                            <option value="default">Default</option>
                                            <option value="most_karma_today">Most received Karma Today</option>
                                            <option value="most_karma_this_month">Most received Karma this Month</option>
                                            <option value="most_karma_all_time">All time received Karma</option>
                                        </select>
                                    </div>
                                    <div class="text-end mb-2 mt-2">
                                        <button class="btn btn-dark rounded btn-md" id="sort_btn">Sort</button>
                                        <button class="btn btn-secondary rounded" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="back-to-top cursor-pointer"><i class="uil uil-angle-up"></i></div>
                </div>
            </div> 
        </div>