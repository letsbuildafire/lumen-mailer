@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left" ng-switch="user.id || '_undefined_'">
                    <span ng-switch-default>Edit @{{ user.username }}</span>
                    <span ng-switch-when="_undefined_">New @{{ dict.TITLE_USERS | lowercase }}</span>
                </h5>
            </div>
            <div class="top-bar-right">
                <md-button ng-click="remove(user, $event)" ng-if="canRemove(user)" class="md-mini md-raised md-warn">
                    <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                    <md-tooltip md-direction="left">
                        Remove
                    </md-tooltip>
                </md-button>
                <div class="inline-block">
                    <md-button ng-click="back()" ng-hide="userForm.$dirty" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                    <md-button ng-show="userForm.$dirty" ng-click="back()" class="md-raised md-primary md-hide">
                        <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                        Cancel
                    </md-button>
                    <md-button md-theme="extended" ng-click="saveOrCreate(userForm, $event)" ng-disabled="userForm.$pristine || !userForm.$valid" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                        Save
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="userForm" class="la-form la-form-users" novalidate>
        <fieldset name="primary-fields">
            <div class="row">
                <md-subheader>
                    <span class="serif">Basic Details</span>
                </md-subheader>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>First Name <small class="lead">*</small></label>
                        <input md-maxlength="32" required name="first_name" ng-model="user.first_name" />
                        <div ng-messages="userForm.first_name.$error" role="alert">
                            <div ng-message="required">A first name is required.</div>
                            <div ng-message="md-maxlength">A first name cannot be longer than 32 characters.</div>
                        </div>
                    </md-input-container>
                </div>
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Last Name</label>
                        <input md-maxlength="32" name="last_name" ng-model="user.last_name" />
                        <div ng-messages="userForm.last_name.$error" role="alert">
                            <div ng-message="md-maxlength">A first name cannot be longer than 32 characters.</div>
                        </div>
                    </md-input-container>
                </div>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Username <small class="lead">*</small></label>
                        <input required md-maxlength="32" ng-pattern="/^[a-zA-Z]\B\w{4,31}$/" name="username" ng-model="user.username" />
                        <div ng-messages="userForm.username.$error" role="alert">
                            <div ng-message-exp="['required', 'md-maxlength', 'pattern']">A username must be between 5 and 32 characters, begin with a letter and may contain letters, numbers, and underscores.</div>
                        </div>
                    </md-input-container>
                </div>
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Email <small class="lead">*</small></label>
                        <input required type="email" name="email" ng-model="user.email" md-maxlength="255" ng-pattern="/^.+@.+\..+$/" />
                        <div ng-messages="userForm.email.$error" role="alert">
                            <div ng-message-exp="['required', 'md-maxlength', 'pattern']">
                                The email address must be less than 255 characters long and be a valid e-mail address.
                            </div>
                        </div>
                    </md-input-container>
                </div>
            </div>
            <div class="row" ng-if="$root.user.role === 'ADMIN'">
                <div class="columns small-12 medium-6">
                    <md-input-container>
                        <label>Role</label>
                        <md-select ng-model="user.role">
                            <md-option ng-repeat="role in roles" value="@{{role.value}}">
                                @{{role.label}}
                            </md-option>
                        </md-select>
                        <br>
                    </md-input-container>
                </div>
            </div>
            <div class="row signature-editor" ng-if="user.role === 'CONTENTADMIN' || user.role === 'ADMIN'">
                <div class="columns small-12">
                    <label class="hint">
                        Signature
                    </label>
                </div>
                <div class="columns small-12">
                    <text-angular class="no-margin-bottom" ng-model="user.signature" ta-toolbar="[['formatSelect','bold','italics','ul','ol','insertLink','html']]" maxlength="21844" name="signature"></text-angular>
                    <div ng-messages="userForm.signature.$error" role="alert">
                        <div ng-message="maxlength">A signature cannot be longer than 21844 characters.</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <md-subheader>
                    <span class="serif">Password</span>
                </md-subheader>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Password <small class="lead">*</small></label>
                        <input ng-required="!user.id" type="password" md-minlength="8" md-maxlength="32" name="password" ng-model="user.password" />
                        <div ng-messages="userForm.password.$error" role="alert">
                            <div ng-message="required">A password is required.</div>
                            <div ng-message-exp="['md-maxlength', 'md-minlength']">
                                The password must be between 8 and 32 characters long.
                            </div>
                        </div>
                    </md-input-container>
                </div>
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Confirm Password <small class="lead">*</small></label>
                        <input ng-required="!user.id" type="password" name="confirm_password" ng-model="confirm_password" match="user.password"/>
                        <div ng-messages="userForm.confirm_password.$error" role="alert">
                            <div ng-message="required">Please confirm your password.</div>
                            <div ng-message="match">Passwords do not match.</div>
                        </div>
                    </md-input-container>
                </div>
            </div>
        </fieldset>
    </form>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ng-click="back()" ng-hide="userForm.$dirty" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                Back
            </md-button>
            <md-button ng-show="userForm.$dirty" ng-click="back()" class="md-raised md-primary md-hide">
                <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                Cancel
            </md-button>
            <md-button md-theme="extended" ng-click="saveOrCreate(userForm, $event)" ng-disabled="userForm.$pristine || !userForm.$valid" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                Save
            </md-button>
        </div>
    </div>
@stop
