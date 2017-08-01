@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left" ng-switch="address.id || '_undefined_'">
                    <span ng-switch-default>Edit @{{ address.email }}</span>
                    <span ng-switch-when="_undefined_">New @{{ dict.TITLE_ADDRESSES }}</span>
                </h5>
            </div>
            <div class="top-bar-right">
                <div class="inline-block">
                    <md-button ng-if="address.id" ng-click="remove(address, $event)" class="md-raised md-mini md-warn">
                        <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                        <md-tooltip md-direction="left">
                            Remove
                        </md-tooltip>
                    </md-button>
                    <md-button ui-sref="admin.lists.edit({id: list.id})" ng-hide="addressForm.$dirty" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                    <md-button ng-show="addressForm.$dirty" ui-sref='admin.lists.edit({id: list.id})' class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                        Cancel
                    </md-button>
                    <md-button ng-click="saveOrCreate(addressForm, $event)" md-theme="extended" ng-disabled="addressForm.$pristine || !addressForm.$valid" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                        Save
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="addressForm" class="la-form la-form-address">
        <fieldset name="default_fields">
            <div class="row">
                <div class="columns small-12">
                    <md-subheader class="collapse">
                        <span class="serif">Primary Fields</span>
                    </md-subheader>
                </div>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6 float-left">
                    <md-input-container class="md-block">
                        <label>Email <small class="lead">*</small></label>
                        <input required type="email" name="email" ng-model="address.email" md-maxlength="255" ng-pattern="/^.+@.+\..+$/" />
                        <div ng-messages="addressForm.email.$error" role="alert">
                            <div ng-message-exp="['required', 'md-maxlength', 'pattern']">
                                The email address must be less than 255 characters long and be a valid e-mail address.
                            </div>
                        </div>
                    </md-input-container>
                </div>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>First Name <small class="lead">*</small></label>
                        <input md-maxlength="32" required name="first_name" ng-model="address.first_name" />
                        <div ng-messages="addressForm.first_name.$error">
                            <div ng-message="required">A first name is required.</div>
                            <div ng-message="md-maxlength">The first name must be less than 32 characters.</div>
                        </div>
                    </md-input-container>
                </div>
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Last Name</label>
                        <input md-maxlength="32" name="last_name" ng-model="address.last_name" />
                        <div ng-messages="addressForm.last_name.$error">
                            <div ng-message="md-maxlength">The last name must be less than 32 characters.</div>
                        </div>
                    </md-input-container>
                </div>
            </div>
        </fieldset>
        <fieldset name="custom_fields ng-hide" ng-show="list.custom_fields.length">
            <div class="row">
                <div class="columns small-12">
                    <md-subheader class="collapse">
                        <span class="serif">Custom Fields</span>
                    </md-subheader>
                </div>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6 float-left" ng-repeat="field in list.custom_fields">
                    <md-input-container class="md-block">
                        <label>@{{ field.name }} <small ng-if="field.req" class="lead">*</small></label>
                        <input md-maxlength="64" ng-required="field.req" name="@{{ field.name }}" ng-model="address.custom_data[field.name]" />
                        <div ng-messages="addressForm[field.name].$error">
                            <div ng-message="required">This field is required.</div>
                            <div ng-message="md-maxlength">This field must be less than 64 characters.</div>
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
            <md-button ui-sref="admin.lists.edit({id: list.id})" ng-hide="addressForm.$dirty" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                Back
            </md-button>
            <md-button ng-show="addressForm.$dirty" ui-sref="admin.lists.edit({id: list.id})" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                Cancel
            </md-button>
            <md-button ng-click="saveOrCreate(addressForm, $event)" ng-disabled="addressForm.$pristine || !addressForm.$valid" md-theme="extended" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                Save
            </md-button>
        </div>
    </div>
@stop
