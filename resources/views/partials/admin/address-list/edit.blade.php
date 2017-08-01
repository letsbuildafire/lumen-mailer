@extends("partials.admin.layouts.header-content-footer")

@section("header")
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left" ng-switch="list.id || '_undefined_'">
                    <span ng-switch-default>Edit @{{ list.name }}</span>
                    <span ng-switch-when="_undefined_">New @{{ dict.TITLE_LISTS }}</span>
                </h5>
            </div>
            <div class="top-bar-right">
                <div class="inline-block hide-for-small-only">
                    <md-button ng-if="list.id" ui-sref="admin.lists.import({id: list.id})" class="md-mini md-raised md-primary" aria-label="Import">
                        <md-icon md-font-set="material-icons">&#xE2C6;</md-icon>
                        <md-tooltip md-direction="top">
                            Import
                        </md-tooltip>
                    </md-button>
                    <md-button ng-if="list.id && addresses._meta.total" href="/export/lists/@{{list.id}}?token=@{{getUserToken()}}" target="_blank" class="md-mini md-raised md-primary" aria-label="Export">
                        <md-icon md-font-set="material-icons">&#xE2C4;</md-icon>
                        <md-tooltip md-direction="top">
                            Export
                        </md-tooltip>
                    </md-button>
                </div>
                <div class="inline-block">
                    <md-button ng-show="listForm.$dirty" ui-sref="admin.lists.list" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                        Cancel
                    </md-button>
                    <md-button ui-sref="admin.lists.list" ng-hide="listForm.$dirty" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                    <md-button ng-click="saveOrCreate(listForm, $event)" md-theme="extended" ng-disabled="listForm.$pristine || !listForm.$valid" class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                        Save
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section("content")
    <form name="listForm" class="la-form la-form-address_list">
        <fieldset name="primary_fields" class="primary_fields">
            <md-subheader class="collapse">
                <span class="serif">@{{ dict.TITLE_LISTS }} Details</span>
            </md-subheader>
            <div class="row">
                <div class="columns small-12 medium-6">
                    <div class="columns small-10 medium-11 no-padding">
                        <md-input-container class="md-block">
                            <label>Name</label>
                            <md-icon md-font-set="material-icons" aria-label="Required" class="ng-hide">&#xE5CD;</md-icon>
                            <input md-maxlength="64" required name="name" ng-model="list.name" />
                            <div ng-messages="listForm.name.$error">
                                <div ng-message="required">A @{{ dict.TITLE_LISTS | lowercase }} name is required.</div>
                                <div ng-message="md-maxlength">@{{ dict.TITLE_LISTS }} name cannot be longer than 64 characters.</div>
                            </div>
                        </md-input-container>
                    </div>
                </div>
            </div>
            <md-subheader class="collapse">
                <span class="serif">Data Fields</span>
                <md-button ng-click="addCustomField()" class="md-raised md-primary float-right no-margin-right ng-hide" ng-show="$root.user.role === 'ADMIN'">
                    <md-icon class="material-icons">&#xE03B;</md-icon>
                    Add Field
                </md-button>
            </md-subheader>
            <div class="row default_fields">
                <div class="columns small-12 medium-6">
                    <div class="columns small-10 medium-11 no-padding">
                        <md-input-container class="md-block">
                            <label class="required-field">
                                <strong class="md-hint">Required Field</strong>
                            </label>
                            <md-icon md-font-set="material-icons" aria-label="Required" class="ng-hide">&#xE5CD;</md-icon>
                            <input md-maxlength="64" disabled name="placeholder_firstname" ng-model="placeholder.first_name" />
                        </md-input-container>
                    </div>
                </div>
                <div class="columns small-12 medium-6">
                    <div class="columns small-10 medium-11 no-padding">
                        <md-input-container class="md-block">
                            <label class="md-hint">Optional Field</label>
                            <md-icon md-font-set="material-icons" aria-label="Required" class="ng-hide">&#xE5CD;</md-icon>
                            <input md-maxlength="64" disabled name="placeholder_lastname" ng-model="placeholder.last_name" />
                        </md-input-container>
                    </div>
                </div>
            </div>
            <div class="row custom_fields">
                <div class="columns small-12 medium-6 collapse float-left" ng-repeat="field in list.custom_fields">
                    <div class="columns small-10 medium-11 no-padding custom_fields-field">
                        <md-input-container class="md-block no-margin-bottom">
                            <label ng-class="{'required-field': field.req}">
                                <span class="md-hint" ng-hide="field.req">Optional Field</span>
                                <strong class="md-hint md-hide" ng-show="field.req">Required Field</strong>
                            </label>
                            <md-icon md-font-set="material-icons" ng-click="removeCustomField(field, $event)" class="ng-hide" ng-show="$root.user.role === 'ADMIN'" aria-label="Remove">&#xE5CD;</md-icon>
                            <input md-maxlength="64" required name="custom_@{{ $index }}" ng-model="field.name" ng-disabled="$root.user.role !== 'ADMIN'"/>
                            <div ng-messages="listForm['custom_'+$index].$error">
                                <div ng-message="required">A field name is required.</div>
                                <div ng-message="md-maxlength">A field name cannot be longer than 64 characters.</div>
                            </div>
                        </md-input-container>
                    </div>
                    <div class="columns small-2 medium-1 no-padding-left custom_fields-toggle">
                        <md-switch ng-model="field.req" class="md-primary ng-hide" aria-label="Required" ng-show="$root.user.role === 'ADMIN'">
                            <!-- <small class="block text-center md-hint" ng-show="field.req">Required</small>
                            <small class="block text-center md-hint" ng-hide="field.req">Optional</small> -->
                        </md-switch>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset name="addresses" ng-if="list.id">
            <div class="la-list la-list-list_addresses">
                <md-toolbar class="md-table-toolbar alternate block" ng-hide="selected.length || address_filter.show" aria-hidden="false">
                    <md-subheader class="collapse">
                        <span class="serif">@{{ dict.TITLES_ADDRESSES }}</span>
                        <md-button ui-sref="admin.lists.address.new({id: list.id})" class="md-raised md-primary float-right">
                            <md-icon class="material-icons">&#xE7FE;</md-icon>
                            Add <span class="hide-for-small-only">@{{ dict.TITLE_ADDRESSES }}</span>
                        </md-button>
                        <md-button ui-sref="admin.lists.import.raw({id: list.id})" class="md-raised md-mini md-primary float-right" aria-label="Add multiple addresses">
                            <md-icon class="material-icons">&#xE7F0;</md-icon>
                            <md-tooltip md-direction="left">
                                Add Multiple
                            </md-tooltip>
                        </md-button>
                    </md-subheader>
                </md-toolbar>
                <md-toolbar class="md-table-toolbar alternate block" class="alternate ng-hide" ng-show="selected.length" aria-hidden="true">
                    <md-subheader class="collapse">
                        <span class="serif">
                            @{{ selected.length }} @{{ selected.length > 1 ? dict.TITLES_ADDRESSES : dict.TITLE_ADDRESSES | lowercase }} selected
                        </span>
                        <div class="text-center medium-text-right medium-float-right">
                            <md-button class="md-raised md-warn" ng-click="removeMany($event)" aria-label="Delete">
                                <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                Delete
                            </md-button>
                            <md-button class="md-raised md-primary" ng-if="canBlock()" ng-click="blockMany($event)" aria-label="Block">
                                <md-icon md-font-set="material-icons">&#xE033;</md-icon>
                                Block
                            </md-button>
                            <md-button class="md-raised md-accent md-hue-3" ng-if="canUnblock()" ng-click="blockMany($event)" aria-label="Block">
                                <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                                Unblock
                            </md-button>
                        </div>
                    </md-subheader>
                </md-toolbar>
                <md-card class="no-content-panel ng-hide" ng-show="!addresses._meta.total">
                    <md-card-title>
                        <md-card-title-text>
                            <span class="md-headline">
                                <strong>
                                    New @{{ dict.TITLE_LISTS | lowercase }}?
                                </strong>
                            </span>
                            <span class="md-subhead">
                                Have you just created a new @{{ dict.TITLE_LISTS | lowercase }}? 
                                Populate the @{{ dict.TITLE_LISTS | lowercase }} with
                                @{{ dict.TITLE_ADDRESSES | lowercase }} data by importing
                                a standard CSV file. Alternatively, you may also manually
                                enter @{{ dict.TITLES_ADDRESSES | lowercase }} individually
                                or in batches.
                            </span>
                        </md-card-title-text>
                    </md-card-title>
                    <md-card-content>
                        <small>
                            This message will disappear once you have added some 
                            @{{ dict.TITLES_ADDRESSES | lowercase }} to the
                            @{{ dict.TITLE_LISTS | lowercase }}.
                        </small>
                    </md-card-content>
                    <md-card-actions class="text-right">
                        <md-button ng-if="list.id" ui-sref="admin.lists.import({id: list.id})" class="md-raised md-primary">
                            <md-icon md-font-set="material-icons">&#xE2C6;</md-icon>
                            Import
                        </md-button>
                    </md-card-actions>
                </md-card>
                <md-table-container class="ng-hide" ng-show="addresses._meta.total">
                    <table md-table md-row-select multiple ng-model="selected" md-progress="promise">
                        <thead md-head md-order="query.order" md-on-reorder="onOrderChange">
                            <tr md-row>
                                <th md-column md-order-by="email">Email</th>
                                <th md-column md-order-by="last_name">Name</th>
                                <th md-column md-order-by="created_at">Subscribed</th>
                                <th md-column></th>
                            </tr>
                        </thead>
                        <tbody md-body>
                            <tr md-row md-select="address" md-select-id="id" ng-repeat="address in addresses">
                                <td md-cell>@{{ address.email }}</td>
                                <td md-cell>@{{ address.first_name }} @{{ address.last_name }}</td>
                                <td md-cell>@{{ address.created_at | amUtc | amLocal | amDateFormat:'DD-MM-YYYY HH:mm:ss' }}</td>
                                <td md-cell class="force-text-right">
                                    {{-- Force the width so the buttons don't stack on mobile --}}
                                    <div class="inline-block actions actions-wide">
                                        <md-button class="md-raised md-mini md-primary" ui-sref="admin.lists.address.edit({id: list.id, address_id: address.id})" ng-disabled="selected.length" aria-label="Edit">
                                            <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Edit
                                            </md-tooltip>
                                        </md-button>
                                        <md-button md-theme="extended" class="md-raised md-mini md-primary" ng-hide="blocked(address)" ng-click="block(address, $event)" ng-disabled="selected.length" aria-label="Block">
                                            <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Block
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-accent md-hue-3 ng-hide" ng-show="blocked(address)" ng-click="block(address, $event)" ng-disabled="selected.length" aria-label="Block">
                                            <md-icon md-font-set="material-icons">&#xE033;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Unblock
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-warn" ng-click="remove(address, $event)" ng-disabled="selected.length" aria-label="Remove">
                                            <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Remove From List
                                            </md-tooltip>
                                        </md-button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </md-table-container>
                <md-table-pagination md-limit="query.limit" md-limit-options="[10, 20, 50]" md-page="query.page" md-total="@{{ addresses._meta.total }}" md-label="pagination.label" md-on-paginate="onPaginationChange" md-page-select class="ng-hide" ng-show="addresses._meta.total"></md-table-pagination>
            </div>
        </fieldset>
    </form>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <div class="inline-block hide-for-small-only">
                <md-button ng-if="list.id" ui-sref="admin.lists.import({id: list.id})" class="md-mini md-raised md-primary" aria-label="Import">
                    <md-icon md-font-set="material-icons">&#xE2C6;</md-icon>
                    <md-tooltip md-direction="top">
                        Import
                    </md-tooltip>
                </md-button>
                <md-button ng-if="list.id && addresses._meta.total" href="/export/lists/@{{list.id}}?token=@{{$root.token}}" target="_blank" class="md-mini md-raised md-primary" aria-label="Export">
                    <md-icon md-font-set="material-icons">&#xE2C4;</md-icon>
                    <md-tooltip md-direction="top">
                        Export
                    </md-tooltip>
                </md-button>
            </div>
            <div class="inline-block">
                <md-button ui-sref="admin.lists.list" ng-hide="listForm.$dirty" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                    Back
                </md-button>
                <md-button ng-show="listForm.$dirty" ui-sref="admin.lists.list" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CD;</md-icon>
                    Cancel
                </md-button>
                <md-button ng-click="saveOrCreate(listForm, $event)" md-theme="extended" ng-disabled="listForm.$pristine || !listForm.$valid" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                    Save
                </md-button>
            </div>
        </div>
    </div>
@stop
