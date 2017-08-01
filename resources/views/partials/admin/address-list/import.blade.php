@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    Import to @{{ list.name }}
                </h5>
            </div>
            <div class="top-bar-right">
                <md-button ui-sref="admin.lists.edit({id: list.id})" ng-hide="addresses.count" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                    Back
                </md-button>
                <md-button ng-show="addresses.count" ui-sref="admin.lists.edit({id: list.id})" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                    Cancel
                </md-button>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="listForm" class="la-form la-form-address_import" novalidate>
        <fieldset name="primary_fields">
            <div class="form-step form-step-file-select" ng-class="{active: step === 'file-select'}">
                <md-toolbar class="form-step-toolbar">
                    <md-subheader>
                        <span class="float-left">
                            <span class="step-indicator text-center">1</span>
                        </span>
                        <span class="serif">To begin importing addresses, </span>
                        <md-button class="md-raised md-primary" ng-class="{'md-success': input_file !== null}" aria-label="Select File">
                            Select
                            <input name="file-source" type="file" value="Choose File" watch-change="fileChanged" accept="text/csv"/>
                        </md-button>
                        <span class="serif"> a valid CSV file to read from.</span>
                    </md-subheader>
                </md-toolbar>
            </div>
            <div class="form-step form-step-header-row" ng-class="{active: step === 'header-option'}">
                <md-toolbar class="form-step-toolbar">
                    <md-subheader>
                        <span class="float-left">
                            <span class="step-indicator text-center">2</span>
                        </span>
                        <span class="serif">Does the CSV file selected have a header row for field names?</span>
                    </md-subheader>
                </md-toolbar>
                <div class="form-step-body small-only-no-padding-left ng-hide" ng-show="input_file !== null">
                    <div class="row collapse header-row-toggle">
                        <div class="columns small-12 text-center medium-text-left">
                            <button class="block medium-inline-block text-center" ng-class="{emphasis: !use_list_fields}" ng-disabled="input_file === null" ng-click="toggleHeader(false, $event)">
                                Yes, there is a header row
                            </button>
                            <md-switch ng-model="use_list_fields" ng-disabled="input_file === null"  class="md-primary middle-align inline-block medium-no-margin-bottom" aria-label="Header Row Present"></md-switch>
                            <button class="block medium-inline-block text-center" ng-class="{emphasis: use_list_fields}" ng-disabled="input_file === null" ng-click="toggleHeader(true, $event)">
                                No, there's no header row
                            </button>&nbsp;
                        </div>
                    </div>
                    <div class="row collapse">
                        <div class="columns small-12">
                            <md-button scroll-to-item scroll-to=".form-step-parse" class="md-raised md-primary float-right" ng-click="setStep('parse-file')" ng-if="step === 'header-option' && !use_list_fields" aria-label="Continue" ng-disabled="use_list_fields">
                                <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                                Continue
                            </md-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-step form-step-list-fields" ng-if="use_list_fields" ng-class="{active: step === 'header-option'}">
                <md-toolbar class="form-step-toolbar">
                    <md-subheader>
                        <span class="float-left">
                            <span class="step-indicator text-center">2b</span>
                        </span>
                        <span class="serif">The field order of the file should match the order below.</span>
                    </md-subheader>
                </md-toolbar>
                <div class="form-step-body small-only-no-padding-left">
                    <span class="hint-import-fields text-center medium-text-left colour-primary">
                        <strong>@{{ fields(false, true) }}</strong>
                    </span>
                    <div class="row collapse">
                        <div class="columns small-12">
                            <md-button scroll-to-item scroll-to=".form-step-parse" class="md-raised md-primary float-right" ng-click="setStep('parse-file')" ng-if="step === 'header-option' && use_list_fields" aria-label="Continue" ng-disabled="!use_list_fields">
                                <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                                Continue
                            </md-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-step form-step-parse" ng-class="{active: step === 'parse-file'}">
                <md-toolbar class="form-step-toolbar">
                    <md-subheader>
                        <span class="float-left">
                            <span class="step-indicator text-center">3</span>
                        </span>
                        <span class="serif">To preview addresses to import,</span>
                        <md-button class="md-raised md-primary" ng-click="parseFile($event)" ng-disabled="input_file === null || step !== 'parse-file'" aria-label="Parse Text">
                            Parse
                        </md-button>
                        <span class="serif"> data from the selected file.</span>
                    </md-subheader>
                </md-toolbar>
            </div>
            <div class="form-step form-step-preview" ng-class="{active: step === 'preview-addresses'}">
                <md-toolbar class="form-step-toolbar">
                    <md-subheader>
                        <span class="float-left">
                            <span class="step-indicator text-center">4</span>
                        </span>
                        <span class="serif">Confirm @{{ dict.TITLES_ADDRESSES | lowercase }} and </span>
                        <md-button class="md-raised md-mini md-warn" ng-disabled="!addresses.count" aria-label="Remove"><md-icon md-font-set="material-icons">&#xE92B;</md-icon></md-button>
                        <span class="serif"> remove unwanted entries.</span>
                    </md-subheader>
                </md-toolbar>
                <div class="form-step-body no-padding-left ng-hide" ng-show="addresses.count">
                    <md-table-container>
                        <table md-table>
                            <thead md-head md-order="query.order">
                                <tr md-row>
                                    <th md-column ng-repeat="field in fields() track by $index" md-order-by="@{{ field }}">
                                        @{{ field }}
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody md-body>
                                <tr md-row ng-repeat="address in addresses.data | orderBy: query.order | limitTo: query.limit: (query.page - 1) * query.limit">
                                    <td md-cell ng-repeat="field in fields() track by $index">@{{ address[field] }}</td>
                                    <td md-cell class="force-text-right">
                                        <md-button class="md-raised md-mini md-warn" ng-click="remove(address, $event)" ng-disabled="selected.length" aria-label="Remove">
                                            <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                            <md-tooltip md-direction="left">
                                                Remove From @{{ dict.TITLE_LISTS }}
                                            </md-tooltip>
                                        </md-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </md-data-table-container>
                    <md-table-pagination md-limit-options="[20, 50, 100]" md-limit="query.limit" md-page="query.page" md-label="pagination.label" md-total="@{{ addresses.count }}" ng-if="addresses.count"></md-table-pagination>
                    <div class="row collapse">
                        <div class="columns small-12 collapse">
                            <md-button scroll-to-item scroll-to=".form-step-confirm" class="md-raised md-primary float-right" ng-click="setStep('confirm')" ng-if="step === 'preview-addresses'" aria-label="Continue" ng-disabled="!addresses.count">
                                <md-icon md-font-set="material-icons">&#xE5CF;</md-icon>
                                Continue
                            </md-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-step form-step-confirm" ng-class="{active: step === 'confirm'}">
                <md-toolbar class="form-step-toolbar">
                    <md-subheader>
                        <span class="float-left">
                            <span class="step-indicator text-center">5</span>
                        </span>
                        <md-button ng-click="import($event)" ng-disabled="!addresses.count || step !== 'confirm'" md-theme="extended" class="md-raised md-primary">Import</md-button>
                        <span class="serif">@{{ dict.TITLES_ADDRESSES | lowercase }} to the @{{ list.name }} @{{ dict.TITLE_LISTS | lowercase }}.</span>
                    </md-subheader>
                </md-toolbar>
            </div>
        </fieldset>
    </form>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.lists.edit({id: list.id})" ng-hide="addresses.count" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                Back
            </md-button>
            <md-button ng-show="addresses.count" ui-sref="admin.lists.edit({id: list.id})" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                Cancel
            </md-button>
        </div>
    </div>
@stop
