@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left" ng-switch="template.id || '_undefined_'">
                    <span ng-switch-default>Edit @{{ template.name }}</span>
                    <span ng-switch-when="_undefined_">New @{{ dict.TITLE_TEMPLATES }}</span>
                </h5>
            </div>
            <div class="top-bar-right">
                <div class="inline-block">
                    <md-button ng-click="remove(template, $event)" ng-if="template.id" class="md-mini md-raised md-warn">
                        <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                        <md-tooltip md-direction="left">
                            Remove
                        </md-tooltip>
                    </md-button>
                    <md-button ui-sref="admin.templates.list" ng-hide="templateForm.$dirty" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                    <md-button ng-show="templateForm.$dirty" ui-sref="admin.templates.list" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                        Cancel
                    </md-button>
                    <md-button ng-click="saveOrCreate(templateForm, $event)" ng-disabled="templateForm.$pristine || !templateForm.$valid" md-theme="extended" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                        Save
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="templateForm" class="la-form la-form-templates" novalidate>
        <div class="row">
            <div class="columns small-12">
                <md-subheader class="collapse">
                    <span class="serif">@{{ dict.TITLE_TEMPLATES }} Details</span>
                </md-subheader>
            </div>
        </div>
        <div class="row">
            <div class="columns small-12" ng-class="{'medium-7': templateForm.source.$valid}">
                <div class="row">
                    <div class="columns small-12">
                        <md-input-container class="md-block">
                            <label>@{{ dict.TITLE_TEMPLATES }} Name <small class="lead">*</small></label>
                            <input md-maxlength="64" required name="name" ng-model="template.name" />
                            <div ng-messages="templateForm.name.$error">
                                <div ng-message="required">A @{{ dict.TITLE_TEMPLATES | lowercase }} name is required.</div>
                                <div ng-message="md-maxlength">A @{{ dict.TITLE_TEMPLATES | lowercase }} name must be less than 64 characters.</div>
                            </div>
                        </md-input-container>
                    </div>
                    <div class="columns small-12">
                        <md-input-container class="md-block">
                            <label>Source HTML Folder <small class="lead">*</small></label>
                            <input maxlength="255" required name="source" ng-model="template.source" ng-model-options="{ debounce: 400 }" ng-pattern="/^[\w-\/]*?$/" />
                            <div ng-messages="templateForm.source.$error">
                                <div ng-message-exp="['required', 'pattern']">A valid source HTML folder is required</div>
                            </div>
                        </md-input-container>
                    </div>
                </div>
                <div class="row">
                    <div class="columns small-12">
                        <label class="field-label">Default Content</label>
                    </div>
                    <div class="columns small-12 template-content">
                        <text-angular ng-model="template.default_content" fields="custom_fields" ta-toolbar="[['placeholder','placeholderWithDefault','formatSelect','bold','italics','ul','ol','html']]" maxlength="21844" name="default_content"></text-angular>
                        <div ng-messages="templateForm.default_content.$error">
                            <div ng-message="maxlength">Default content cannot be longer than 21844 characters.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns small-12 medium-5" ng-if="templateForm.source.$valid">
                <div class="row">
                    <div class="columns small-12">
                        <label class="field-label">Preview</label>
                    </div>
                    <div class="columns small-11 medium-12 float-center">
                        <div class="preview-frame">
                            <iframe ng-src="@{{ '/templates/' + template.source }}" sandbox></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.templates.list" ng-hide="templateForm.$dirty" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                Back
            </md-button>
            <md-button ng-show="templateForm.$dirty" ui-sref="admin.templates.list" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                Cancel
            </md-button>
            <md-button ng-click="saveOrCreate(templateForm, $event)" ng-disabled="templateForm.$pristine || !templateForm.$valid" md-theme="extended" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                Save
            </md-button>
        </div>
    </div>
@stop
