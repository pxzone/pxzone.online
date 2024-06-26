
    <div class="">
    <!-- NAVBAR START -->
    <div class="_nav np bg-nav">
        <nav class="navbar navbar-expand navbar-dark home-index-default bg-nav k-header" id="_home_navbar">
            <div class="container nav-container">
                <!-- logo -->
                <div class="web-view">
                    <div class="navbar-brand me-lg-5 cursor-pointer" >
                        <a onclick="_accessPage('')" class="cursor-pointer"><img src="<?=base_url('assets/images/logo/hh-logo.webp')?>" alt="<?=$siteSetting['website_name']?> Logo" class="hh-logo"></a>
                    </div>
                </div>

                <div class="mobile-view d-flex justify-content-between w-100">
                    <div class="p-2 mt-1 ">
                        <div class="navbar-brand me-lg-5 cursor-pointer">
                            <a class="cursor-pointer" onclick="_accessPage('')"><img class="mm-logo" src="<?=base_url('assets/images/logo/hh-logo.webp')?>" alt="<?=$siteSetting['website_name']?>"></a>
                        </div>
                    </div>
                    <div class="div-nav-logo p-2">
                        
                    </div>
                    <div class="p-2 mt-1">
                        <button class="mobile-view rounded-pill font-13 btn btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#"
                        aria-controls="" aria-expanded="false" aria-label="Toggle navigation" onclick="openNav()">
                        ☰&nbsp;Menu
                        </button>
                    </div>
                </div>
                <div class="menu menu_mm trans_300 mobile-view bg-nav">
                    <div class="menu_container menu_mm">
                        <div class="page_menu_content">
                            <ul class="page_menu_nav menu_mm">
                                <li class="page_menu_item menu_mm cursor-pointer"><a onclick="_accessPage('')">Home</a></li>
                                <li class="page_menu_item menu_mm cursor-pointer"><a onclick="_accessPage('blog')">Blog</a></li>
                                <li class="page_menu_item menu_mm cursor-pointer"><a onclick="_accessPage('tools')">Tools</a></li>
                                <li class="page_menu_item menu_mm cursor-pointer"><a onclick="_accessPage('about')">About Us</a></li>
                                <?php if (isset($this->session->user_id)) {?><li class="page_menu_item menu_mm cursor-pointer"><a onclick="_accessPage('account/dashboard')">Account</a></li><?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="menu_close">✕</div>
                </div>

                <div class="collapse navbar-collapse " id="navbarNavDropdown">
                    <ul class="navbar-nav me-auto align-items-right text-uppercase fw-500 web-view">
                    </ul>
                    <ul class="navbar-nav ms-auto align-items-center fw-500 web-view">
                        <li class="nav-item mx-lg-1">
                            <a class="nav-link cursor-pointer c-white" onclick="_accessPage('blog')">Blog</a>
                        </li>
                        <li class="nav-item mx-lg-1">
                            <a class="nav-link cursor-pointer c-white" onclick="_accessPage('tools')">Tools</a>
                        </li>
                        <li class="nav-item mx-lg-1">
                            <a class="nav-link cursor-pointer c-white" onclick="_accessPage('about')">About Us</a>
                        </li>
                        <?php if(isset($this->session->user_id)) {?>
                        <li class="dropdown text-capitalize">
                            <a class="nav-link dropdown-toggle nav-user arrow-none me-0 margin-top-3" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="uil-user-circle font-40"></i>
                                <span>
                                    <span class="account-user-name text-capitalize"></span>
                                    <span class="account-position text-capitalize"></span>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                                <a href="<?=base_url('account/dashboard')?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    <span>My Account</span>
                                </a>
                                <a href="<?=base_url('account/settings')?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-cog me-1"></i>
                                    <span>Settings</span>
                                </a>
                                <a href="<?=base_url('logout')?>" class="dropdown-item notify-item">
                                    <i class="mdi mdi-logout me-1"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </li>
                        <?php } ?> 
                    </ul>

                </div>
            </div>
        </nav>
        <!-- NAVBAR END -->

                                            
    </div>
    <!-- END HERO -->
    <!-- NAVBAR END -->
