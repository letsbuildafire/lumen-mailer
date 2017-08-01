@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left" ng-switch="article.id || '_undefined_'">
                    <span ng-switch-default>Edit @{{ article.title }}</span>
                    <span ng-switch-when="_undefined_">New @{{ dict.TITLE_ARTICLES | lowercase }}</span>
                </h5>
            </div>
            <div class="top-bar-right">
                <md-button ng-click="remove(article, $event)" ng-if="article.id" class="md-raised md-mini md-warn">
                    <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                    <md-tooltip md-direction="left">
                        Remove
                    </md-tooltip>
                </md-button>
                <div class="inline-block">
                    <md-button ui-sref="admin.help.index.list.all" ng-hide="articleForm.$dirty" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                    <md-button ng-show="articleForm.$dirty" ui-sref="admin.help.index.list.all" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                        Cancel
                    </md-button>
                    <md-button ng-click="saveOrCreate(articleForm, $event)" ng-disabled="articleForm.$pristine || !articleForm.$valid" md-theme="extended" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                        Save
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="articleForm" class="la-form la-form-article" novalidate>
        <fieldset name="primary-fields">
            <div class="row">
                <md-subheader>
                    <span class="serif">@{{ dict.TITLE_ARTICLES }} Details</span>
                </md-subheader>
            </div>
            <div class="row">
                <div class="columns small-12 medium-6">
                    <md-input-container class="md-block">
                        <label>Title <small class="lead">*</small></label>
                        <input required md-maxlength="64" name="title" ng-model="article.title" />
                        <div ng-messages="articleForm.title.$error" role="alert">
                            <div ng-message="required">A title is required.</div>
                            <div ng-message="md-maxlength">A title cannot be longer than 64 characters.</div>
                        </div>
                    </md-input-container>
                </div>
                <div class="columns small-12 medium-6">
                    <md-input-container>
                        <label>Category <small class="lead">*</small></label>
                        <md-select ng-model="article.section">
                            <md-option ng-repeat="section in sections | orderBy:'label'" value="@{{section.value}}">
                                @{{section.label}}
                            </md-option>
                        </md-select>
                    </md-input-container>
                </div>
            </div>
            <div class="row article-editor">
                <div class="columns small-12">
                    <label class="field-label">Content <small class="lead">*</small></label>
                </div>
                <div class="columns small-12">
                    <text-angular required ng-model="article.content" ta-toolbar="[['formatSelect','bold','italics','ul','ol','insertLink','html']]" ng-maxlength="21844"></text-angular>
                    <div ng-messages="articleForm.content.$error" role="alert">
                        <div ng-message-exp="['required', 'ng-maxlength']" class="md-warn">
                            @{{ dict.TITLE_ARTICLES }} content is required and must be less than 21844 characters long.
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.help.index.list.all" ng-hide="articleForm.$dirty" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                Back
            </md-button>
            <md-button ng-show="articleForm.$dirty" ui-sref="admin.help.index.list.all" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                Cancel
            </md-button>
            <md-button ng-click="saveOrCreate(articleForm, $event)" ng-disabled="articleForm.$pristine || !articleForm.$valid" md-theme="extended" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                Save
            </md-button>
        </div>
    </div>
@stop
