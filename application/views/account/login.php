
	<div id="_web_container">
		<div class="other-section bg-secondary">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 margin-top-40">
                        <div class=" mb-5">
                            <div class="-header pt-2 pb-2 text-center bg-dark">
                                <span>
                                    <a href="#" onclick="_accessPage('')">
                                        <h1 class="c-white fw-600 font-23 pt-1 pb-1">
                                            Welcome
                                        </h1>
                                    </a>
                                </span>
                            </div>

                            <div class="-body p-4 bg-light">
                                <div class="text-center w-75 m-auto">
                                    <!-- <h1 class="text-dark-50 text-center mt-0 fw-bold mb-2 font-23">Log In Account</h1> -->
                                </div>
                                <form action="#" class="bg-light" id="_login_form">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control bg-light" id="username"  name="username" required="" placeholder="Enter your Username" />
                                        <label for="username" class="fw-400">Username</label>
                                    </div>
                
                                    <div class="form-floating ">
                                        <input type="password" id="_password" name="password" class="form-control bg-light" placeholder="Enter your password" />
                                        <label for="password" class="fw-400">Password</label>
                                    </div>
                                    <div class="mt-1 text-sm-start">
                                        <div class="mt-1 pointer-cursor">
                                            <small id="_show_password" onclick="_showPassword()">
                                            	<span class="pointer-cursor" ></span><i class="uil-eye "></i> Show Password
                                            </small>
                                            <small hidden id="_hide_password" onclick="_hidePassword()">
                                            	<span class="pointer-cursor" ></span><i class="uil-eye-slash "></i> Hide Password
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mt-2 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" value="<?=$login_token?>" name="remember_login" id="remember_login">
                                            <label class="form-check-label" for="remember_login">Remember me</label>
                                        </div>
                                    </div>
                                    <div class="mb-3 mb-0 text-center">
                                        <button class="btn btn-dark text-white rounded k-btn btn-lg col-lg-12 col-12 font-17" id="_login_btn" type="submit"> Log In </button>
                                    </div>
                                    <input type="hidden" name="<?=$csrf_data['name']?>" value="<?=$csrf_data['hash']?>">
                                    <input type="hidden" name="last_url" value="<?=(isset($_GET['return'])) ? $_GET['return'] : '';?>">
                                    
                                </form>
                            </div> 
                        </div>
                        <!-- end card -->
                    </div> 
                </div>
            </div>
        </div>
	</div>
