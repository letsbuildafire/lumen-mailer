@extends('partials.admin.layouts.header-content-footer')

@section('header')
    <div class="top-bar action-bar">
        <div class="row colapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    @{{ dict.TITLES_LISTS }}
                </h5>
            </div>
            <div class="top-bar-right">
                <md-button ng-hide="filter.show" class="md-mini md-raised md-primary" ng-click="filter.show = true">
                    <md-icon class="material-icons">&#xE152;</md-icon>
                    <md-tooltip md-direction="left">
                        Filter
                    </md-tooltip>
                </md-button>
                <md-button aria-hidden="true" ng-show="filter.show && !selected.length" class="md-mini md-raised md-accent" ng-click="resetFilter()">
                    <md-icon class="material-icons">&#xE14C;</md-icon>
                </md-button>
                <md-button ui-sref="admin.lists.new" class="md-raised md-primary">
                    <md-icon class="material-icons">&#xE03B;</md-icon>
                    Add @{{ dict.TITLE_LISTS }}
                </md-button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="la-list la-list-address_list" ng-hide="!filter.show && !lists._meta.total">
        <md-toolbar class="md-filter-toolbar ng-hide" ng-show="filter.show && !selected.length">
            <div class="top-bar-filters float-none">
                <form flex name="filterForm" class="filter-form medium-text-left">
                    <md-input-container>
                        <label>Search by Name</label>
                        <md-icon class="material-icons">&#xE8B6;</md-icon>
                        <input ng-model="query.q" name="q" ng-model-options="filter.options" type="text" >
                    </md-input-container>
                </form>
            </div>
        </md-toolbar>
        <md-toolbar class="md-table-toolbar alternate ng-hide" ng-show="selected.length" aria-hidden="true">
            <md-subheader class="collapse">
                <span class="serif">
                    <ng-pluralize count="selected.length"
                        when="{'one': '{} @{{ dict.TITLE_LISTS | lowercase }} selected',
                               'other': '{} @{{ dict.TITLES_LISTS | lowercase }} selected'}">
                    </ng-pluralize>
                </span>
                <md-button class="md-raised md-warn float-right" ng-click="removeMany($event)" aria-label="Delete">
                    <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                    <ng-pluralize count="selected.length"
                        when="{'one': 'Delete @{{ dict.TITLE_LISTS | lowercase }}',
                               'other': 'Delete @{{ dict.TITLE_LISTS | lowercase }}s'}">
                    </ng-pluralize>
                </md-button>
            </md-subheader>
        </md-toolbar>
        <md-table-container>
            <table md-table md-row-select multiple ng-model="selected" md-progress="promise">
                <thead md-head md-order="query.order" md-on-reorder="onOrderChange">
                    <tr md-row>
                        <th md-column md-order-by="name">Name</th>
                        <th md-column md-order-by="created_at">Created</th>
                        <th md-column md-order-by="updated_at">Updated</th>
                        <th md-column></th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr md-row md-select="list" md-select-id="id" ng-repeat="list in lists">
                        <td md-cell>@{{ list.name }}</td>
                        <td md-cell>@{{ list.created_at | amUtc | amLocal | amDateFormat:'DD-MM-YYYY HH:mm:ss' }}</td>
                        <td md-cell title="@{{ list.updated_at | amUtc | amLocal }}">@{{ list.updated_at | amUtc | amLocal | amTimeAgo }}</td>
                        <td md-cell class="force-text-right">
                            {{-- Force the width so the buttons don't stack on mobile --}}
                            <div class="inline-block actions">
                                <md-button class="md-raised md-mini md-primary" ui-sref="admin.lists.edit({id: list.id})" ng-disabled="selected.length" aria-label="Edit">
                                    <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                                    <md-tooltip md-direction="top">
                                        Edit
                                    </md-tooltip>
                                </md-button>
                                <md-button class="md-raised md-mini md-warn" ng-click="remove(list, $event)" ng-disabled="selected.length" aria-label="Remove">
                                    <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                    <md-tooltip md-direction="top">
                                        Remove
                                    </md-tooltip>
                                </md-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </md-table-container>
        <md-table-pagination md-limit="query.limit" md-limit-options="[10, 20, 50]" md-page="query.page" md-total="@{{ lists._meta.total }}" md-label="pagination.label" md-on-paginate="onPaginationChange" md-page-select></md-table-pagination>
    </div>
    <md-card class="no-content-panel ng-hide" ng-show="!filter.show && !lists._meta.total">
        <md-card-title>
            <md-card-title-text>
                <span class="md-headline">
                    <strong>
                        New to {!! env('APP_NAME') !!}?
                    </strong>
                </span>
                <span class="md-subhead">
                    You're currently in the @{{ dict.TITLES_LISTS }} section 
                    of {!! env('APP_NAME') !!}, and it looks like you have not 
                    created any @{{ dict.TITLES_LISTS | lowercase }} yet.
                    To get started, create a new @{{ dict.TITLE_LISTS | lowercase }}
                    to use when sending a @{{ dict.TITLE_EMAILERS | lowercase }}.
                </span>
            </md-card-title-text>
        </md-card-title>
        <md-card-content>
            <small>
                This message will disappear once you have created a 
                @{{ dict.TITLE_LISTS | lowercase }}.
            </small>
        </md-card-content>
        <md-card-actions class="text-right">
            <md-button ui-sref="admin.help.index.list.all" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE0C6;</md-icon>
                Help
            </md-button>
            <md-button ui-sref="admin.lists.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                Add @{{ dict.TITLE_LISTS }}
            </md-button>
        </md-card-actions>
    </md-card>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.lists.new" class="md-raised md-primary">
                <md-icon class="material-icons">&#xE03B;</md-icon>
                Add @{{ dict.TITLE_LISTS }}
            </md-button>
        </div>
    </div>
@stop
