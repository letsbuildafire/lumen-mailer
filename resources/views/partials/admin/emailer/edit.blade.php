@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left" ng-switch="emailer.id || '_undefined_'">
                    <span ng-switch-default>Edit @{{ emailer.subject }}</span>
                    <span ng-switch-when="_undefined_">New @{{ dict.TITLE_EMAILERS }}</span>
                </h5>
            </div>
            <div class="top-bar-right">
                <div class="inline-block">
                    <md-button aria-label="Remove" ng-click="remove(emailer, $event)" ng-hide="hasStatus(emailer, ['RUNNING', 'PAUSED', 'COMPLETED'])" ng-if="emailer.id" class="md-mini md-raised md-warn">
                        <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                        <md-tooltip md-direction="left">
                            Remove
                        </md-tooltip>
                    </md-button>
                    <md-button aria-label="Approve" md-theme="extended" ng-click="toggleApproval(emailer, $event)" 
                        ng-show="emailer.id && !hasStatus(emailer, ['RUNNING', 'PAUSED', 'COMPLETED'])" 
                        class="md-primary ng-hide" ng-class="{'md-raised': emailer.approved}">
                        <span class="ng-hide" ng-show="!emailer.approved">Approve</span>
                        <span class="ng-hide" ng-show="emailer.approved">Approved</span>
                    </md-button>
                </div>
                <div class="inline-block">
                    <md-button aria-label="Back" ui-sref="admin.emailers.list" ng-hide="emailerForm.$dirty" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                    <md-button aria-label="Cancel" ng-show="emailerForm.$dirty" ui-sref="admin.emailers.list" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                        Cancel
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="emailerForm" class="la-form la-form-emailer" novalidate>
        <fieldset class="primary-fields form-step form-step-emailer-details" ng-class="{active: step == 'emailer-details'}" ng-form="primary_fields">
            <md-toolbar class="form-step-toolbar">
                <md-subheader class="text-right">
                    <span class="float-left text-left">
                        <span class="step-indicator emailer-step-indicator text-center">1</span>
                        <span class="serif">Enter @{{ dict.TITLE_EMAILERS | lowercase }} details</span>
                    </span>
                    <md-button class="md-raised md-mini md-primary ng-hide" ng-click="setStep('emailer-details')" aria-label="Edit" ng-show="step !== 'emailer-details'">
                        <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                        <md-tooltip md-direction="left">
                            Edit
                        </md-tooltip>
                    </md-button>
                </md-subheader>
            </md-toolbar>
            <div class="form-step-body small-only-no-padding-left" ng-hide="step !== 'emailer-details'">
                <div class="row">
                    <div class="columns small-12 medium-6">
                        <md-input-container class="md-block">
                            <label>Subject <small class="lead">*</small></label>
                            <input required md-maxlength="255" name="subject" ng-model="emailer.subject" />
                            <div ng-messages="primary_fields.subject.$error" role="alert">
                                <div ng-message-exp="['required', 'md-maxlength']">
                                    A subject is required and must be less than 255 characters long.
                                </div>
                            </div>
                        </md-input-container>
                    </div>
                </div>
                <div class="row">
                    <div class="columns small-12 medium-6">
                        <md-input-container class="md-block">
                            <label>Return Name </label>
                            <input md-maxlength="255" name="return_name" ng-model="emailer.return_name"/>
                            <div ng-messages="primary_fields.return_name.$error" role="alert">
                                <div ng-message="md-maxlength">
                                    The return name must be less than 255 characters long.
                                </div>
                            </div>
                        </md-input-container>
                    </div>
                    <div class="columns small-12 medium-6">
                        <md-input-container class="md-block">
                            <label>Return Address <small class="lead">*</small></label>
                            <input md-maxlength="255" required name="return_address" ng-model="emailer.return_address" ng-pattern="/^.+@.+\..+$/" />
                            <div ng-messages="primary_fields.return_address.$error" role="alert">
                                <div ng-message-exp="['required', 'md-maxlength', 'pattern']">
                                    The return address must be less than 255 characters long and be a valid e-mail address.
                                </div>
                            </div>
                        </md-input-container>
                    </div>
                </div>
                <div class="emailer-step-actions ng-hide" ng-show="step === 'emailer-details'">
                    <md-button class="md-raised md-primary float-right" ng-click="setStep('template')" aria-label="Continue" ng-disabled="!primary_fields.$valid">
                        <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                        Continue
                    </md-button>
                </div>
            </div>
        </fieldset>
        <fieldset class="template-list form-step form-step-templates" ng-class="{active: step === 'template'}" ng-form="template_fields">
            <md-toolbar class="form-step-toolbar">
                <md-subheader class="text-right">
                    <span class="float-left text-left">
                        <span class="step-indicator emailer-step-indicator text-center">2</span>
                        <span class="serif">Select a @{{ dict.TITLE_TEMPLATES | lowercase}}</span>
                    </span>
                    <md-button ng-hide="step !== 'template' || template_filter.show" class="md-mini md-raised md-primary" ng-click="template_filter.show = true">
                        <md-icon class="material-icons">&#xE152;</md-icon>
                        <md-tooltip md-direction="left">
                            Filter
                        </md-tooltip>
                    </md-button>
                    <md-button aria-hidden="true" ng-show="template_filter.show && step === 'template'" class="md-mini md-raised md-accent" ng-click="resetTemplateFilter()">
                        <md-icon class="material-icons">&#xE14C;</md-icon>
                    </md-button>
                    <md-button class="md-raised md-mini md-primary ng-hide" ng-click="setStep('template')" aria-label="Edit" ng-show="step !== 'template' && emailer.template_id">
                        <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                        <md-tooltip md-direction="left">
                            Edit
                        </md-tooltip>
                    </md-button>
                </md-subheader>
            </md-toolbar>
            <div class="form-step-body no-padding-left ng-hide" ng-show="step == 'template'">
                <div class="la-list la-list-grid la-templates">
                    <md-toolbar class="md-filter-toolbar ng-hide" ng-show="template_filter.show">
                        <div class="top-bar-filters float-none">
                            <div class="filter-form medium-text-left" ng-form="template_filter_form">
                                <md-input-container>
                                    <label>Search by Name</label>
                                    <md-icon class="material-icons">&#xE8B6;</md-icon>
                                    <input ng-model="template_query.q" name="q" ng-model-options="template_filter.options" type="text" >
                                </md-input-container>
                            </div>
                        </div>
                    </md-toolbar>
                    <md-table-container>
                        <table md-table md-progress="template_promise">
                            <thead md-head></thead>
                            <tbody md-body class="row">
                                <tr md-row ng-repeat="template in templates" class="columns small-3" ng-class="{selected: emailer.template_id === template.id}">
                                    <td md-cell class="template-preview">
                                        <iframe ng-src="@{{ '/templates/' + template.source }}" sandbox></iframe>
                                    </td>
                                    <td md-cell class="template-details">
                                        <div class="template-name">@{{ template.name }}</div>
                                        <div class="template-selectable">
                                            <label for="template_id_@{{ template.id }}" ng-click="toggleTemplate($event, template)">
                                                <input type="radio" required id="template_id_@{{ template.id }}" ng-model="emailer.template_id" name="template_id" ng-value="template.id">
                                                <md-button ng-show="emailer.template_id === template.id" class="md-fab md-primary" md-theme="extended" aria-label="Use @{{ template.name }}">
                                                    <md-icon md-font-set="material-icons">&#xE876;</md-icon>
                                                </md-button>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </md-table-container>
                    <md-table-pagination md-limit="template_query.limit" md-page="template_query.page" md-limit-options="[4]" md-total="@{{ templates._meta.total }}" md-on-paginate="onTemplatePaginationChange" md-page-select></md-table-pagination>
                </div>
                <div class="emailer-step-actions" ng-show="step === 'template'">
                    <md-button class="md-raised md-primary float-right" ng-click="setStep('lists')" aria-label="Continue" ng-disabled="!emailer.template_id">
                        <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                        Continue
                    </md-button>
                </div>
            </div>
        </fieldset>
        <fieldset class="customer-list-list form-step form-step-lists" ng-class="{active: step === 'lists'}" ng-form="lists_fields">
            <md-toolbar class="form-step-toolbar">
                <md-subheader class="text-right">
                    <span class="float-left text-left">
                        <span class="step-indicator emailer-step-indicator text-center">3</span>
                        <span class="serif">Select @{{ dict.TITLES_LISTS | lowercase }}</span>
                    </span>
                    <md-button ng-hide="step !== 'lists' || list_filter.show" class="md-mini md-raised md-primary" ng-click="list_filter.show = true">
                        <md-icon class="material-icons">&#xE152;</md-icon>
                        <md-tooltip md-direction="left">
                            Filter
                        </md-tooltip>
                    </md-button>
                    <md-button aria-hidden="true" ng-show="list_filter.show && step === 'lists'" class="md-mini md-raised md-accent" ng-click="resetListFilter()">
                        <md-icon class="material-icons">&#xE14C;</md-icon>
                    </md-button>
                    <md-button class="md-raised md-mini md-primary ng-hide" ng-click="setStep('lists')" aria-label="Edit" ng-show="step !== 'lists' && emailer.template_id">
                        <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                        <md-tooltip md-direction="left">
                            Edit
                        </md-tooltip>
                    </md-button>
                </md-subheader>
            </md-toolbar>
            <div class="form-step-body no-padding-left ng-hide" ng-show="step === 'lists'">
                <div class="la-list la-list-short-grid la-customer-lists">
                    <md-toolbar class="md-table-toolbar no-padding text-right">
                        <md-subheader class="short-subheader">
                            <span class="count ng-hide" ng-show="emailer.lists.length">
                                <strong>
                                    <ng-pluralize count="emailer.lists.length"
                                        when="{'one': '{} @{{ dict.TITLE_LISTS | lowercase }} selected',
                                               'other': '{} @{{ dict.TITLE_LISTS | lowercase }}s selected'}">
                                    </ng-pluralize>
                                </strong>
                            </span>
                        </md-subheader>
                    </md-toolbar>
                    <md-toolbar class="md-filter-toolbar ng-hide" ng-show="list_filter.show">
                        <div class="top-bar-filters float-none">
                            <div class="filter-form medium-text-left" ng-form="list_filter_form">
                                <md-input-container>
                                    <label>Search by Name</label>
                                    <md-icon class="material-icons">&#xE8B6;</md-icon>
                                    <input ng-model="list_query.q" name="q" ng-model-options="list_filter.options" type="text" >
                                </md-input-container>
                            </div>
                        </div>
                    </md-toolbar>
                    <md-table-container>
                        <table md-table md-progress="list_promise">
                            <thead md-head></thead>
                            <tbody md-body class="row">
                                <tr md-row ng-repeat="list in lists" class="columns small-6 medium-4 large-3" ng-class="{selected: emailer.lists.indexOf(list.id) !== -1}">
                                    <td md-cell class="selectable">
                                        <md-checkbox class="md-primary" ng-checked="listSelected(list)" ng-required="!emailer.lists.length" ng-click="toggleList($event, list)" ng-value="list.id" aria-label="@{{list.name}}">
                                            @{{ list.name }}
                                        </md-checkbox>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </md-table-container>
                    <md-table-pagination md-limit="list_query.limit" md-page="list_query.page" md-limit-options="[12, 24]" md-total="@{{ lists._meta.total }}" md-on-paginate="onListPaginationChange" md-page-select></md-table-pagination>
                </div>
                <div class="emailer-step-actions" ng-show="step === 'lists'">
                    <md-button class="md-raised md-primary float-right" ng-click="setStep('content')" aria-label="Continue" ng-disabled="!emailer.lists.length">
                        <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                        Continue
                    </md-button>
                </div>
            </div>
        </fieldset>
        <fieldset class="content-fields form-step form-step-content-fields" ng-class="{active: step === 'content'}" ng-form="content_fields">
            <md-toolbar class="form-step-toolbar">
                <md-subheader class="text-right">
                    <span class="float-left text-left">
                        <span class="step-indicator emailer-step-indicator text-center">4</span>
                         <span class="serif">Edit @{{ dict.TITLE_EMAILERS | lowercase }} content</span>
                    </span>
                    <md-button class="md-raised md-mini md-primary ng-hide" ng-click="setStep('content')" aria-label="Edit" ng-show="step !== 'content' && emailer.content.length">
                        <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                        <md-tooltip md-direction="left">
                            Edit
                        </md-tooltip>
                    </md-button>
                </md-subheader>
            </md-toolbar>
            <div class="form-step-body small-only-no-padding-left ng-hide" ng-show="step == 'content'">
                <div class="content-field-wrapper">
                    <div class="md-block">
                        <text-angular name="emailer_content" required ng-model="emailer.content" fields="custom_fields" ta-toolbar="[['placeholder','placeholderWithDefault','formatSelect','bold','italics','ul','ol','html']]" ng-maxlength="21844"></text-angular>
                        <div ng-messages="content_fields.content.$error" role="alert">
                            <div ng-message-exp="['required', 'md-maxlength']">
                                @{{ dict.TITLE_EMAILERS }} content is required and must be less than 21844 characters long.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="emailer-step-actions" ng-show="step === 'content'">
                    <md-button class="md-raised md-primary float-right" ng-click="setStep('signature')" aria-label="Continue" ng-disabled="!content_fields.$valid">
                        <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                        Continue
                    </md-button>
                </div>
            </div>
        </fieldset>
        <fieldset class="content-fields form-step form-step-signature-fields" ng-class="{active: step === 'signature'}" ng-form="signature_fields">
            <md-toolbar class="form-step-toolbar">
                <md-subheader class="text-right">
                    <span class="float-left text-left">
                        <span class="step-indicator emailer-step-indicator text-center">5</span>
                        <span class="serif">Edit your signature</span>
                    </span>
                    <md-button class="md-raised md-mini md-primary ng-hide" ng-click="setStep('signature')" aria-label="Edit" ng-show="step !== 'signature' && emailer.content.length && emailer.signature.length">
                        <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                        <md-tooltip md-direction="left">
                            Edit
                        </md-tooltip>
                    </md-button>
                </md-subheader>
            </md-toolbar>
            <div class="form-step-body small-only-no-padding-left ng-hide" ng-show="step === 'signature'">
                <div class="row content-field-wrapper">
                    <div class="columns small-12">
                        <text-angular name="signature_content" required ng-model="emailer.signature" fields="custom_fields" ta-toolbar="[['placeholder','placeholderWithDefault','formatSelect','bold','italics','ul','ol','html']]" maxlength="21844"></text-angular>
                        <div ng-messages="signature_fields.signature.$error" role="alert">
                            <div ng-message-exp="['required', 'md-maxlength']">
                                Signature content is required and must be less than 21844 characters long.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="emailer-step-actions" ng-show="step === 'signature'">
                    <md-button class="md-raised md-primary float-right" ng-click="setStep('schedule')" aria-label="Continue" ng-disabled="!signature_fields.$valid">
                        <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                        Continue
                    </md-button>
                </div>
            </div>
        </fieldset>
        <fieldset class="schedule-fields form-step form-step-schedule-fields" ng-class="{active: step === 'schedule'}" ng-form="schedule_form">
            <md-toolbar class="form-step-toolbar">
                <md-subheader class="text-right">
                    <span class="float-left text-left">
                        <span class="step-indicator emailer-step-indicator text-center">6</span>
                        <span class="serif">Preview &amp; Send</span>
                    </span>
                    <md-button class="md-raised md-mini md-primary ng-hide" ng-click="setStep('schedule')" aria-label="Edit" ng-show="step !== 'schedule' && emailerForm.$valid">
                        <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                        <md-tooltip md-direction="left">
                            Edit
                        </md-tooltip>
                    </md-button>
                </md-subheader>
            </md-toolbar>
            <div class="form-step-body small-only-no-padding-left ng-hide" ng-show="step === 'schedule'">
                <div class="row preview-panel">
                    <div class="columns small-12 text-center">
                        <md-input-container class="preview-recipients">
                            <label>Preview recipients</label>
                            <input md-maxlength="255" name="preview_address" ng-model="preview_address" ng-pattern="/^((\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)*([,])*)*$/" />
                            <div class="hint" ng-if="schedule_form.preview_address.$valid || schedule_form.preview_address.$pristine">Separate multiple addresses with a comma</div>
                            <div ng-messages="schedule_form.preview_address.$error" ng-if="schedule_form.preview_address.$error" role="alert">
                                <div ng-message-exp="['md-maxlength', 'pattern']">
                                    Please provide valid, comma-separated email addresses.
                                </div>
                            </div>
                        </md-input-container>
                        <md-button aria-label="Email Preview" ng-disabled="!emailerForm.$valid || !preview_address.length || !schedule_form.preview_address.$valid" ng-click="preview($event, true)" class="md-raised md-primary">
                            <md-icon md-font-set="material-icons">&#xE0BE;</md-icon>
                            Email
                        </md-button>
                        <md-button aria-label="Live Preview" ng-disabled="!emailerForm.$valid" ng-click="preview($event)" class="md-raised md-primary">
                            <md-icon md-font-set="material-icons">&#xE89D;</md-icon>
                            Live Preview
                        </md-button>
                    </div>
                </div>
                <div class="row schedule-panel">
                    <div class="schedule-option text-center inline-block" ng-class="{active: emailer.send_now === false}">
                        <div>
                            <strong class="block">I want to send this @{{ dict.TITLE_EMAILERS | lowercase }} later.</strong>
                            <small>The @{{ dict.TITLE_EMAILERS | lowercase }} will need to be approved.</small>
                        </div>
                        <md-button class="md-raised md-primary" aria-label="Send Later" ng-click="sendNow($event, false)">
                            <md-icon md-font-set="material-icons">&#xE878;</md-icon>
                            Schedule
                        </md-button>
                    </div>
                    <div class="schedule-option text-center inline-block" ng-class="{active: emailer.send_now}">
                        <div>
                            <strong class="block">I want to send this @{{ dict.TITLE_EMAILERS | lowercase }} now.</strong>
                            <small>The @{{ dict.TITLE_EMAILERS | lowercase }} will be automatically approved.</small>
                        </div>
                        <md-button class="md-raised md-primary" aria-label="Send Now" ng-click="sendNow($event, true)">
                            <md-icon md-font-set="material-icons">&#xE163;</md-icon>
                            Send Now
                        </md-button>
                    </div>
                </div>
                <div class="row time-panel ng-hide" ng-show="emailer.send_now === false">
                    <div class="columns small-12 text-center time-controls small-only-no-padding">
                        <time-date-picker theme="/tpl/admin/directive/datetime" name="distribute_at" ng-model="emailer.distribute_at" display-mode="full" autosave="true" mindate="@{{ minimumDateTime() }}"></time-date-picker required>
                    </div>
                </div>
                <div class="row confirm-panel ng-hide" ng-show="emailer.send_now !== null">
                    <div class="confirm-dialog text-center">
                        <div ng-hide="emailer.send_now">
                            <strong class="block">
                                Schedule the @{{ dict.TITLE_EMAILERS | lowercase }} for distribution.
                            </strong>
                            <small>
                                Once confirmed, the @{{ dict.TITLE_EMAILERS | lowercase }} will
                                be scheduled for delivery, but will still require approval 
                                before sending.
                            </small>
                        </div>
                        <div class="ng-hide" ng-show="emailer.send_now">
                            <strong class="block">
                                Send the @{{ dict.TITLE_EMAILERS | lowercase }} to all of the chosen
                                @{{ dict.TITLE_LISTS | lowercase }}(s) selected now.
                            </strong>
                            <small>
                                Once confirmed, the @{{ dict.TITLE_EMAILERS | lowercase }} will be
                                dispatched immediately.
                            </small>
                        </div>
                        <md-button md-theme="extended" ng-click="saveOrCreate(emailerForm, $event, true)" ng-disabled="!emailerForm.$valid"  class="md-raised md-primary" aria-label="Send Now" ng-click="emailer.send_now = true">
                            <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                            Confirm
                        </md-button>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <div class="inline-block">
                <md-button aria-label="Approve" md-theme="extended" ng-click="toggleApproval(emailer, $event)" 
                    ng-show="emailer.id && !hasStatus(emailer, ['RUNNING', 'PAUSED', 'COMPLETED'])" 
                    class="md-primary ng-hide" ng-class="{'md-raised': emailer.approved}">
                    <span class="ng-hide" ng-show="!emailer.approved">Approve</span>
                    <span class="ng-hide" ng-show="emailer.approved">Approved</span>
                </md-button>
                <md-button ui-sref="admin.emailers.list" ng-hide="emailerForm.$dirty" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                    Back
                </md-button>
                <md-button ng-show="emailerForm.$dirty" ui-sref="admin.emailers.list" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                    Cancel
                </md-button>
            </div>
        </div>
    </div>
    <div class="modal modal-template-preview emailer-preview" ng-if="emailer_preview" ng-click="hide()">
        <div class="columns small-centered text-center template-preview-frame">
            <div class="top-bar template-preview-bar">
                <div class="top-bar-left">
                    <h4>
                        Preview of @{{ emailer.subject }}
                    </h4>
                </div>
                <div class="top-bar-right medium-text-right">
                    <md-button aria-label="Close" ng-click="hide()" class="md-mini md-raised md-accent">
                        <md-icon class="material-icons">&#xE5CD;</md-icon>
                        <md-tooltip md-direction="left">
                            Close
                        </md-tooltip>
                    </md-button>
                </div>
            </div>
            <div la-preview preview-content="emailer_preview"></div>
        </div>
    </div>
@stop
