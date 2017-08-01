@extends('partials.admin.layouts.header-content-footer')

@section('header')
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    @{{ emailer.subject + ' Statistics' }}
                </h5>
            </div>
            <div class="top-bar-right">
                <div class="inline-block">
                    <md-button ng-hide="filter.show" class="md-mini md-raised md-primary" ng-click="filter.show = true">
                        <md-icon class="material-icons">&#xE152;</md-icon>
                        <md-tooltip md-direction="left">
                            Filter
                        </md-tooltip>
                    </md-button>
                    <md-button aria-hidden="true" ng-show="filter.show && !selected.length" class="md-mini md-raised md-accent" ng-click="resetFilter()">
                        <md-icon class="material-icons">&#xE14C;</md-icon>
                    </md-button>
                    <md-button ui-sref='admin.emailers.list' class="md-raised md-primary">
                        <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                        Back
                    </md-button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="data-emailer_stats">
        <div class="row small-up-2 medium-up-3 large-up-6 emailer_stats-grid">
            <div class="columns">
                <p class="emailer_stat status-UNSENT">
                    <span class="label">Unsent</span>
                    <strong>@{{ emailer.api_sending_status_numbers.Unsent || 0 }}</strong>
                </p>
            </div>
            <div class="columns">
                <p class="emailer_stat status-SENT">
                    <span class="label">Sent</span>
                    <strong>@{{ emailer.api_sending_status_numbers.Sent || 0 }}</strong>
                </p>
            </div>
            <div class="columns">
                <p class="emailer_stat status-BOUNCED">
                    <span class="label">Bounced</span>
                    <strong>@{{ emailer.api_sending_status_numbers.Bounced || 0 }}</strong>
                </p>
            </div>
            <div class="columns">
                <p class="emailer_stat status-ACCEPTED">
                    <span class="label">Accepted</span>
                    <strong>
                        @{{ emailer.api_sending_status_numbers.Accepted || 0 }}
                        (@{{ emailer.api_sending_status_numbers.Deferred || 0 }})
                        <md-tooltip md-direction="bottom">
                            Total (Deferred)
                        </md-tooltip>
                    </strong>
                </p>
            </div>
            <div class="columns">
                <p class="emailer_stat stat-OPENS">
                    <span class="label">Opens</span>
                    <strong>
                        @{{ emailer.opens || 0 }}
                        (@{{ emailer.unique_opens || 0 }})
                        <md-tooltip md-direction="bottom">
                            Total (Unique)
                        </md-tooltip>
                    </strong>
                </p>
            </div>
            <div class="columns">
                <p class="emailer_stat stat-CLICKS">
                    <span class="label">Clicks</span>
                    <strong>
                        @{{ emailer.clicks || 0 }}
                        (@{{ emailer.unique_clicks || 0 }})
                        <md-tooltip md-direction="bottom">
                            Total (Unique)
                        </md-tooltip>
                    </strong>
                </p>
            </div>
        </div>
    </div>
    <md-divider></md-divider>
    <div class="la-list la-emailer_stats">
        <md-toolbar class="md-filter-toolbar ng-hide" ng-show="filter.show">
            <div class="top-bar-filters float-none">
                <form name="filterForm" class="filter-form medium-text-left">
                    <md-input-container>
                        <label>Filter by Status</label>
                        <md-icon class="material-icons">&#xE427;</md-icon>
                        <md-select ng-model="query.status" name="status">
                            <md-option value="" disabled>
                                All
                            </md-option>
                            <md-option ng-repeat="status in statuses" value="@{{ status.value }}">
                                @{{ status.label }}
                            </md-option>
                        </md-select>
                    </md-input-container>
                    <md-input-container>
                        <label>Search by Email</label>
                        <md-icon class="material-icons">&#xE8B6;</md-icon>
                        <input ng-model="query.q" name="q" ng-model-options="filter.options" type="text" >
                    </md-input-container>
                </form>
            </div>
        </md-toolbar>
        <md-table-container>
            <table md-table>
                <thead md-head md-order="query.order" md-on-reorder="onOrderChange" md-progress="promise">
                    <tr md-row>
                        <th md-column md-order-by="addresses.email">Email</th>
                        <th md-column md-order-by="opens">Opens</th>
                        <th md-column md-order-by="clicks">Clicks</th>
                        <th md-column md-order-by="status">Status</th>
                        <th md-column>Extended Status</th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr md-row ng-repeat="stat in stats" class="emailer_stat emailer_stat-@{{ stat.status }}">
                        <td md-cell>@{{ stat.address.email }}</td>
                        <td md-cell class="stat-OPENS"><span>@{{ stat.opens }}</span></td>
                        <td md-cell class="stat-CLICKS"><span>@{{ stat.clicks }}</span></td>
                        <td md-cell class="stat-STATUS">@{{ stat.status }}</td>
                        <td md-cell class="stat-EXSTATUS">@{{ stat.extended_status }}</td>
                    </tr>
                </tbody>
            </table>
        </md-table-container>
        <md-table-pagination md-limit="query.limit" md-page="query.page" md-total="@{{ stats._meta.total }}" md-limit-options="[50,100,500]" md-on-paginate="onPaginationChange" md-page-select></md-data-table-pagination>
    </div>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref='admin.emailers.list' class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE5CB;</md-icon>
                Back
            </md-button>
        </div>
    </div>
@stop
