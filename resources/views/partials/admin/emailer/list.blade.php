@extends('partials.admin.layouts.header-content-footer')

@section('header')
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    @{{ dict.TITLES_EMAILERS }}
                </h5>
            </div>
            <div class="top-bar-right">
                <md-button ng-hide="filter.show" class="md-raised md-primary md-mini" ng-click="filter.show = true">
                    <md-icon class="material-icons">&#xE152;</md-icon>
                    <md-tooltip md-direction="top">
                        Filter
                    </md-tooltip>
                </md-button>
                <md-button aria-hidden="true" ng-show="filter.show && !selected.length" class="md-raised md-accent md-mini" ng-click="resetFilter()">
                    <md-icon class="material-icons">&#xE14C;</md-icon>
                </md-button>
                <md-button class="md-raised md-primary md-mini" ng-class="{'md-accent': compact, 'md-primary': !compact}" ng-click="compact = !compact">
                    <md-icon class="material-icons" ng-hide="compact">&#xE896;</md-icon>
                    <md-icon class="material-icons ng-hide" ng-show="compact">&#xE8EF;</md-icon>
                    <md-tooltip md-direction="top">
                        <span ng-hide="compact">Toggle Compact View</span>
                        <span class="ng-hide" ng-show="compact">Toggle Expanded View</span>
                    </md-tooltip>
                </md-button>
                <md-button ui-sref="admin.emailers.new" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                    Add @{{ dict.TITLE_EMAILERS }}
                </md-button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="la-list la-list-block la-emailers" ng-hide="!filter.show && !emailers._meta.total">
        <md-toolbar class="md-filter-toolbar ng-hide" ng-show="filter.show">
            <div class="top-bar-filters">
                <form name="filterForm" class="filter-form medium-text-left">
                    <md-input-container>
                        <label>Search by Subject</label>
                        <md-icon class="material-icons">&#xE8B6;</md-icon>
                        <input ng-model="query.q" name="q" ng-model-options="filter.options" type="text" >
                    </md-input-container>
                    <md-input-container>
                        <label>Filter by Status</label>
                        <md-icon class="material-icons">&#xE922;</md-icon>
                        <md-select ng-model="query.status" name="status">
                            <md-option value="">
                                All
                            </md-option>
                            <md-option ng-repeat="status in statuses" value="@{{ status.value }}">
                                @{{ status.label }}
                            </md-option>
                        </md-select>
                    </md-input-container>
                </form>
            </div>
        </md-toolbar>
        <md-table-container>
            <table md-table md-progress="promise">
                <thead md-head md-order="query.order" md-on-reorder="onOrderChange">
                    <tr md-row>
                        <th md-column class="columns small-6 float-none" md-order-by="subject">Subject</th>
                        <th md-column class="columns small-3 float-none" md-order-by="distribute_at">Distribute At</th>
                        <th md-column class="columns small-3 float-none" md-order-by="status">Status</th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr md-row ng-repeat="emailer in emailers" class="row emailers_list-item emailers_list-item-status-@{{emailer.status}}">
                        <td md-cell class="emailer-name columns small-12 medium-3">
                            <div class="row">
                                <div class="columns small-12">
                                    <md-subheader>
                                        <strong>@{{ emailer.subject }}</strong>
                                    </md-subheader>
                                    <div class="actions emailer-actions">
                                        <md-button class="md-raised md-mini md-primary ng-hide" ng-show="hasStatus(emailer, ['RUNNING'])" ng-click="pause(emailer, $event)" aria-label="Pause">
                                            <md-icon md-font-set="material-icons">&#xE034;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Pause
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-accent md-hue-3 ng-hide" ng-show="hasStatus(emailer, ['PAUSED'])" ng-click="start(emailer, $event)" aria-label="Start">
                                            <md-icon md-font-set="material-icons">&#xE037;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Start
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-primary ng-hide" md-theme="extended" ng-show="hasStatus(emailer, ['APPROVED','PENDING'])" ng-click="toggleApproval(emailer, $event)" aria-label="Unapprove">
                                            <md-icon md-font-set="material-icons">&#xE5CA;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Unapprove
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-accent md-hue-3" ng-hide="hasStatus(emailer, ['APPROVED','PENDING','PAUSED','RUNNING','COMPLETED'])" ng-click="toggleApproval(emailer, $event)" aria-label="Approve">
                                            <md-icon md-font-set="material-icons">&#xE033;</md-icon>
                                            <md-tooltip md-direction="top">
                                               Approve
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-primary" ng-show="hasStatus(emailer, ['PAUSED','RUNNING','COMPLETED'])" ui-sref="admin.emailers.stats({id: emailer.id})" aria-label="Stats">
                                            <md-icon md-font-set="material-icons">&#xE01D;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Stats
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-primary" ui-sref="admin.emailers.edit({id: emailer.id})" ng-hide="hasStatus(emailer, ['RUNNING','COMPLETED'])" aria-label="Edit">
                                            <md-icon md-font-set="material-icons">&#xE254;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Edit
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-primary" href="/emails/@{{emailer.id}}" target="_blank" ng-show="hasStatus(emailer, ['RUNNING','COMPLETED'])" aria-label="View">
                                            <md-icon md-font-set="material-icons">&#xE8F4;</md-icon>
                                            <md-tooltip md-direction="top">
                                                View
                                            </md-tooltip>
                                        </md-button>
                                        <md-button class="md-raised md-mini md-warn" ng-click="remove(emailer, $event)" ng-show="hasStatus(emailer, ['UNAPPROVED','APPROVED','PENDING'])" aria-label="Remove">
                                            <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                            <md-tooltip md-direction="top">
                                                Remove
                                            </md-tooltip>
                                        </md-button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td colspan="2" md-cell class="emailer-details columns small-12 medium-9">
                            <div class="row">
                                <div class="columns small-12 medium-7 distribute_at">
                                    <span class="label">Distribute at: </span>
                                    <strong>@{{ emailer.distribute_at | amUtc | amLocal | amDateFormat:'DD-MM-YYYY HH:mm:ss Z' }}</strong>
                                </div>
                                <div class="columns small-12 medium-5 status">
                                    <span class="label">Status: </span>
                                    <strong>@{{ emailer.status | titlecase }}</strong>
                                </div>
                            </div>
                            <div class="row">
                                <div class="columns small-12 separator">
                                    <md-divider></md-divider>
                                </div>
                            </div>
                            <div class="row emailer-progress" ng-hide="compact">
                                <div class="columns small-3 medium-2 large-2">
                                    <span class="label">Progress: </span>
                                </div>
                                <div class="columns small-9 medium-9 large-10">
                                    <md-progress-linear md-mode="determinate" value="@{{ emailer.progress }}" ng-class="{'md-success': emailer.progress === 100}"></md-progress-linear>
                                </div>
                            </div>
                            <div class="row" ng-hide="compact">
                                <div class="columns small-12 separator">
                                    <md-divider></md-divider>
                                </div>
                            </div>
                            <div class="row emailer-statistics">
                                <div class="columns small-12 float-right">
                                    <div class="row small-up-2 medium-up-3 large-up-6">
                                        <div class="columns no-padding-left">
                                            <span class="label">Unsent: </span><br>
                                            <strong>@{{ emailer.api_sending_status_numbers.Unsent || 0 }}</strong>
                                        </div>
                                        <div class="columns no-padding-left">
                                            <span class="label">Sent: </span><br>
                                            <strong ng-hide="emailer.status === 'COMPLETED'">@{{ emailer.api_sending_status_numbers.Sent || 0 }}</strong>
                                            <strong class="ng-hide" ng-show="emailer.status === 'COMPLETED'">@{{ emailer.api_sending_status_numbers.Accepted + emailer.api_sending_status_numbers.Deferred + emailer.api_sending_status_numbers.Bounced || 0 }}</strong>
                                        </div>
                                        <div class="columns no-padding-left">
                                            <span class="label">Bounced: </span><br>
                                            <strong>@{{ emailer.api_sending_status_numbers.Bounced || 0 }}</strong>
                                        </div>
                                        <div class="columns no-padding-left">
                                            <span class="label">Accepted: </span><br>
                                            <strong>@{{ emailer.api_sending_status_numbers.Accepted + emailer.api_sending_status_numbers.Deferred || 0 }}</strong>
                                        </div>
                                        <div class="columns no-padding-left">
                                            <span class="label">Opens: </span><br>
                                            <strong>
                                                @{{ emailer.opens || 0 }}
                                                (@{{ emailer.unique_opens || 0 }})
                                            </strong>
                                            <md-tooltip md-direction="bottom">
                                                Total (Unique)
                                            </md-tooltip>
                                        </div>
                                        <div class="columns no-padding-left">
                                            <span class="label">Clicks: </span><br>
                                            <strong>
                                                @{{ emailer.clicks || 0 }}
                                                (@{{ emailer.unique_clicks || 0 }})
                                            </strong>
                                            <md-tooltip md-direction="bottom">
                                                Total (Unique)
                                            </md-tooltip>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" ng-hide="compact">
                                <div class="columns small-12 separator">
                                    <md-divider></md-divider>
                                </div>
                            </div>
                            <div class="row emailer-customer-lists" ng-hide="compact">
                                <div class="columns small-12 medium-3 large-2">
                                    <span class="label">@{{ dict.TITLES_LISTS }}: </span>
                                </div>
                                <div class="columns small-12 medium-9 large-10">
                                    <md-chips class="action-chips" ng-model="emailer.lists" readonly="true">
                                        <md-chip-template class="chip-action" ui-sref="admin.lists.edit({id: $chip.id})">@{{ $chip.name }}</md-chip-template>
                                    </md-chips>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </md-table-container>
        <md-table-pagination md-limit="query.limit" md-limit-options="[10, 20, 50]" md-page="query.page" md-label="pagination.label" md-total="@{{ emailers._meta.total }}" md-on-paginate="onPaginationChange" md-page-select></md-table-pagination>
    </div>
    <md-card class="no-content-panel ng-hide" ng-show="!filter.show && !emailers._meta.total">
        <md-card-title>
            <md-card-title-text>
                <span class="md-headline">
                    <strong>
                        New to {!! env('APP_NAME') !!}?
                    </strong>
                </span>
                <span class="md-subhead">
                    You're currently in the @{{ dict.TITLES_EMAILERS }} section 
                    of {!! env('APP_NAME') !!}, and it looks like you have not 
                    created any @{{ dict.TITLES_EMAILERS | lowercase }} yet.
                    To get started, create a new @{{ dict.TITLE_EMAILERS | lowercase }}
                    using a @{{ dict.TITLE_LISTS | lowercase }} that you have 
                    created and one of the predefined @{{ dict.TITLES_TEMPLATES | lowercase }}.
                </span>
            </md-card-title-text>
        </md-card-title>
        <md-card-content>
            <small>
                This message will disappear once you have created a 
                @{{ dict.TITLE_EMAILERS | lowercase }}.
            </small>
        </md-card-content>
        <md-card-actions class="text-right">
            <md-button ui-sref="admin.help.index.list.all" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE0C6;</md-icon>
                Help
            </md-button>
            <md-button ui-sref="admin.emailers.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                Add a @{{ dict.TITLE_EMAILERS }}
            </md-button>
        </md-card-actions>
    </md-card>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button class="md-mini md-raised md-primary" ng-click="compact = !compact">
                <md-icon class="material-icons" ng-hide="compact">&#xE896;</md-icon>
                <md-icon class="material-icons ng-hide" ng-show="compact">&#xE8EF;</md-icon>
                <md-tooltip md-direction="left">
                    <span ng-hide="compact">Toggle Compact View</span>
                    <span class="ng-hide" ng-show="compact">Toggle Expanded View</span>
                </md-tooltip>
            </md-button>
            <md-button ui-sref="admin.emailers.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                Add @{{ dict.TITLE_EMAILERS }}
            </md-button>
        </div>
    </div>
@stop
