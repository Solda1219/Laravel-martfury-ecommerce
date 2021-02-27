
<div class="ps-my-account" id= 'registerForm'>
    <div class="container">
        <div id= "emailUseAlert" class="alert alert-warning alert-dismissible fade show" style= "display: none" role="alert">
          <strong>Email in use</strong> You typed email in use by another user. Please try with another email.
        </div>
        <div id= "phoneinvalidAlert" class="alert alert-warning alert-dismissible fade show" style= "display: none" role="alert">
          <strong>Phone Number invalid</strong> Please input valid phone number.
        </div>
        <form class="ps-form--account ps-tab-root">
            <div class="ps-form__content">
                <h4>{{ __('Register An Account') }}</h4>
                <div class="form-group">
                    <input class="form-control" name="name" id="txt-name" type="text" value="{{ old('name') }}" placeholder="{{ __('Your Name') }}">
                    <span id= "nameVal" style= "display: none;" class="text-danger">Name field is required!</span>
                </div>
                <div class="form-group">
                    <input class="form-control" name="email" id="txt-email" type="email" value="{{ old('email') }}" placeholder="{{ __('Your Email') }}">
                    <span id= "emailVal" style= "display: none;" class="text-danger">Email field is required!</span>
                </div>
                <div class="form-group">
                    <input class="form-control" name="phone" id="txt-phone" type="text" value="{{ old('phone') }}" placeholder="Your Phone Number">
                    <span id= "phoneVal" style= "display: none;" class="text-danger">Phone field is required!</span>
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password" id="txt-password" placeholder="{{ __('Password') }}">
                    <span id= "passVal" style= "display: none;" class="text-danger">Password field is required!</span>
                    <span id= "passGreater" style= "display: none;" class="text-danger">Password require at least 6!</span>
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password_confirmation" id="txt-password-confirmation" placeholder="{{ __('Password') }}">
                    <span id= "repassVal" style= "display: none;" class="text-danger">You Must insert the same password!</span>
                </div>
                <div class="form-group">
                    <div class="ps-checkbox">
                        <input class="form-check-input" type="checkbox" name="agree_terms_policy" id="terms-policy" value="1">
                        <label class="form-check-label" for="terms-policy"><span>{{ __('I agree to terms & Policy.') }}</span></label>
                    </div>
                </div>
                @if (setting('enable_captcha') && is_plugin_active('captcha'))
                    {!! Captcha::display() !!}
                @endif
                <div class="form-group">
                    <button class="ps-btn ps-btn--fullwidth" type= "button" id= "signUp">{{ __('Sign up') }}</button>
                </div>

                <div class="form-group">
                    <p class="text-center">{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Log in') }}</a></p>
                </div>
            </div>
            <div class="ps-form__footer">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
            </div>
        </form>
    </div>
</div>


<div id= 'verifyForm' class="ps-my-account" style= "display: none;">

    <div class="container">
        <div id= "verifyfailAlert" class="alert alert-warning alert-dismissible fade show" style= "display: none" role="alert">
          <strong>Something went wrong</strong> Verification code is incorrect. Didn't you get verification code? Then resend.
          
        </div>
        <form class="ps-form--account ps-tab-root" >
            <div class="ps-form__content">
                <h4>Verify Phone Code</h4>
                <div class="form-group">
                    <input class="form-control" type="text" name="verifyCode" id="txt-verifyCode" placeholder="verification code">
                    @if ($errors->has('verifyCode'))
                        <span class="text-danger">{{ $errors->first('verifyCode') }}</span>
                    @endif
                </div>

                <div class="form-group submit">
                    <button class="ps-btn ps-btn--fullwidth" id= "verify" type="text">Verify</button>
                </div>

                <div class="form-group">
                    <p class="text-center">Do you want to resend? <a href="javascript:;" id= "resend">resend</a></p>
                </div>
            </div>
            <!-- <div class="ps-form__footer">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
            </div> -->
        </form>
    </div>
</div>
<script>
    var veriel = document.getElementById('verify');
    
    if(veriel){
        document.getElementById('verify').addEventListener('click', onVerifyCodeSubmit);
        function onVerifyCodeSubmit(e) {
            e.preventDefault();
            var txtname= document.getElementById('txt-name').value;
            var txtemail= document.getElementById('txt-email').value;
            var txtphone= document.getElementById('txt-phone').value;
            var txtpassword= document.getElementById('txt-password').value;
            var txtrepass= document.getElementById('txt-password-confirmation').value;
            window.verifyingCode = true;
            var code = document.getElementById('txt-verifyCode').value;
            confirmationResult.confirm(code).then(function (result) {
              // User signed in successfully.
              var user = result.user;
              window.verifyingCode = false;
              window.confirmationResult= null;
              $.ajaxSetup({
                headers: {
                  'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
              });
              $.post( "/register", { name: txtname, email: txtemail, phone: txtphone, password: txtpassword } ).then(function (response) {
                    window.location.href="/customer/overview";
                      

                  }, function (response) {

                        // error callback
                        console.log(response);

                    });
            }).catch(function (error) {
              // User couldn't sign in (bad verification code?)
              document.getElementById('verifyfailAlert').style.display= "block";

            });
        }
    }
      
    var signEl= document.getElementById('signUp');
    function resend(){
        console.log("resend btn clicked");
        var txtphone= document.getElementById('txt-phone').value;
        var appVerifier = window.recaptchaVerifier;
        firebase.auth().signInWithPhoneNumber(txtphone, appVerifier)
            .then(function (confirmationResult) {
                window.confirmationResult= confirmationResult;
                // var confirmationResult= JSON.stringify(confirmationResult);
                // window.localStorage.setItem('confirmationResult', confirmationResult);
                // console.log(JSON.parse(window.localStorage.getItem('confirmationResult')));
                // window.location.href= '/phone-verify?phone='+txtphone;
            }).catch(function (error) {
                if(error.code== "auth/invalid-phone-number"){
                    // location.reload();
                    // alert("Phone Number invalid");
                    document.getElementById("phoneinvalidAlert").style.display= "block";
                    
                }
                else{
                    // alert("another error occured");
                    // console.error("another error", error);
                }
            });
        }
    if(signEl){
        var txtphone= document.getElementById('txt-phone').value;
        signEl.addEventListener('click', onSignUpClick);
        
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier(signUp, {
            'size': 'invisible',
            // 'callback': (response) => {
            //     onSignUpClick();
            // },
        });
        
        function onSignUpClick(){
            console.log('one time called');
            var txtname= document.getElementById('txt-name').value;
            var txtemail= document.getElementById('txt-email').value;
            var txtphone= document.getElementById('txt-phone').value;
            var txtpassword= document.getElementById('txt-password').value;
            var txtrepass= document.getElementById('txt-password-confirmation').value;


            // validation part
            if(document.getElementById('registerForm').style.display!="none"){
                var valFlag= false;
                if(txtname== ''){
                    document.getElementById('nameVal').style.display= "block";
                    valFlag= true;
                }
                else{
                    document.getElementById('nameVal').style.display= "none";
                }
                if(txtemail== ''){
                    document.getElementById('emailVal').style.display= "block";
                    valFlag= true;
                }
                else{
                    document.getElementById('emailVal').style.display= "none";
                }
                if(txtphone== ''){
                    document.getElementById('phoneVal').style.display= "block";
                    valFlag= true;
                }
                else{
                    document.getElementById('phoneVal').style.display= "none";
                }
                if(txtpassword== ''){
                    document.getElementById('passVal').style.display= "block";
                    valFlag= true;
                }
                else{
                    document.getElementById('passVal').style.display= "none";
                }
                if(txtrepass!= txtpassword){
                    document.getElementById('repassVal').style.display= "block";
                    valFlag= true;
                }
                else{
                    document.getElementById('repassVal').style.display= "none";
                }
                if(valFlag== true){
                    return 0;
                }
                
                // This is for email unique
                $.get("/emailUnique", {'email': txtemail})
                    .then(function (response) {

                        // success callback
                        console.log(response);
                        if(response== "in use"){
                            document.getElementById("emailUseAlert").style.display= "block";
                            // window.localStorage.setItem('valFlag','true');
                            return 0;
                        }
                        else{
                            
                            
                            // recaptchaVerifier.render().then(function(widgetId) {
                            //     window.recaptchaWidgetId = widgetId;

                            // });
                            var appVerifier = window.recaptchaVerifier;
                            
                            firebase.auth().signInWithPhoneNumber(txtphone, appVerifier)
                                .then(function (confirmationResult) {
                                  window.confirmationResult= confirmationResult;
                                  document.getElementById('registerForm').style.display= "none";
                                  document.getElementById('verifyForm').style.display= "block";
                                  // var confirmationResult= JSON.stringify(confirmationResult);
                                  // window.localStorage.setItem('confirmationResult', confirmationResult);
                                  // console.log(JSON.parse(window.localStorage.getItem('confirmationResult')));
                                  // window.location.href= '/phone-verify?phone='+txtphone;
                                }).catch(function (error) {
                                    if(error.code== "auth/invalid-phone-number"){
                                        // location.reload();
                                        // alert("Phone Number invalid");
                                        document.getElementById("phoneinvalidAlert").style.display= "block";
                                        
                                    }
                                    else{
                                        // alert("another error occured");
                                        // console.error("another error", error);
                                    }
                                });
                        }


                    }, function (response) {

                        // error callback
                        console.log(response);

                    });
            }
            
        // console.log('very ok');
        // console.log(signingIn);
        }
    }
    var resendEl= document.getElementById("resend");
    if(resendEl){
        document.getElementById('resend').addEventListener('click', resend);
    }
</script>