    <div id="_web_container" class="dark-theme">
            <div class=" other-section padding-bottom-30 c-dwhite"  >
                <div class="first-section padding-bottom-30 c-dwhite">
                    <div class="container">
                        <div class="text-center">
                            <h1 class="f-h1 mb-2 c-white">AltcoinsTalks Karma Logs</h1>
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
                                            <input type="search" class="form-control" name="search" id="search" placeholder="Search username...">
                                        </div>
                                    </form>                            
                                </div>
                                <div class="col-xl-6">
                                    <div class="text-xl-end mt-xl-0 mt-2">
                                        <button type="button" class="btn rounded btn-secondary mb-2" onclick="fetchKarmaLogs(1,'')"><i class="uil-redo"></i> Refresh</button>
                                    </div>
                                </div> 
                            </div>

                            <div class="mt-2 table-responsive">
                            <table class="table table-centered mb-0 font-14">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Karma Point</th>
                                        <th>Total Karma</th>
                                        <th>Datetime</th>
                                    </tr>
                                </thead>
                                <tbody id="karma_log_tbl">
                                </tbody>
                            </table> 
                            </div>
                        </div> 
                    </div>
                                      
                    <div id="karma_log_pagination"></div>
                    <div class="back-to-top cursor-pointer"><i class="uil uil-angle-up"></i></div>
                </div>


            </div> 
        </div>