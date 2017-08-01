<section class="login-content">
    <div class="login-panel ng-hide" ng-show="!userIsLoggedIn()">
        <div class="login-header text-right">
            <span class="login-client-logo"><span></span></span>
        </div>
        <div class="login-form-header text-center">
            <span class="block">
                <strong>Sign in to continue.</strong>
            </span>
            <span class="block">
                Issues? Contact us! 
                <a href="mailto:{!! env("MAIL_ADMIN_ADDRESS") !!}">
                    {!! env("MAIL_ADMIN_ADDRESS") !!}
                </a>.
            </span>
        </div>
        <div class="login-form">
            <form method="POST" name="loginForm" data-ng-submit="loginForm.$valid && submit()" novalidate>
                <md-input-container class="login-form-field">
                    <label>Username</label>
                    <input ng-model="user.username" name="user" type="text" required>
                    <div ng-messages="loginForm.user.$error" role="alert">
                        <div ng-message="required">Username must not be empty.</div>
                    </div>
                </md-input-container>
                <md-input-container class="login-form-field">
                    <label>Password</label>
                    <input ng-model="user.password" name="pass" type="password" required>
                    <div ng-messages="loginForm.pass.$error" role="alert">
                        <div ng-message="required">Password must not be empty.</div>
                    </div>
                    <span class="hint colour-warn ng-hide" ng-show="$root.last_error.type === 'AUTH'">
                        @{{ $root.last_error.message }}
                    </span>
                </md-input-container>
                <div class="login-form-actions">
                    <div class="login-actions">
                        <md-button type="submit" class="md-raised md-primary float-right">Sign In</md-button>
                        <md-button class="md-flat" style="display:none">Reset password</md-button>
                    </div>
                </div>
            </form>
        </div>
        <div class="login-form-footer text-right">
            <h1>{!! env("APP_NAME") !!}</h1>
            <small>{!! env("APP_VERSION") !!}</small>
        </div>
    </div>
</section>
