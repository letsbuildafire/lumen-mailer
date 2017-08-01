@extends('partials.admin.layouts.header-content-footer')

@section('header')
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    {{ env('TITLES_USERS') }}
                </h5>
            </div>
            <div class="top-bar-right" ng-if="userHasPermission('ADMIN')">
                <md-button ng-hide="selected.length || filter.show" class="md-raised md-mini md-primary" ng-click="filter.show = true">
                    <md-icon class="material-icons">&#xE152;</md-icon>
                    <md-tooltip md-direction="left">
                        Filter
                    </md-tooltip>
                </md-button>
                <md-button aria-hidden="true" ng-show="filter.show && !selected.length" class="md-raised md-mini md-accent" ng-click="resetFilter()">
                    <md-icon class="material-icons">&#xE14C;</md-icon>
                </md-button>
                <md-button ui-sref="admin.users.new" class="md-raised md-primary">
                    <md-icon class="material-icons">&#xE03B;</md-icon>
                    Add {{ env('TITLE_USERS') }}
                </md-button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="la-list la-users">
        <md-toolbar class="md-filter-toolbar ng-hide" ng-show="filter.show && !selected.length">
            <div class="top-bar-filters float-none">
                <form name="filterForm" class="filter-form medium-text-left">
                    <md-input-container>
                        <label>Search by Email</label>
                        <md-icon class="material-icons">&#xE8B6;</md-icon>
                        <input ng-model="query.q" name="q" ng-model-options="filter.options" type="text" >
                    </md-input-container>
                    <md-input-container>
                        <label>Filter by Role</label>
                        <md-icon class="material-icons">&#xE7FB;</md-icon>
                        <md-select ng-model="query.role" name="role">
                            <md-option value="" disabled>
                                All
                            </md-option>
                            <md-option ng-repeat="role in roles" value="@{{ role.value }}">
                                @{{ role.label }}
                            </md-option>
                        </md-select>
                    </md-input-container>
                </form>
            </div>
        </md-toolbar>
        <md-toolbar class="md-table-toolbar alternate ng-hide" ng-show="selected.length" aria-hidden="true">
            <md-subheader class="collapse">
                <span class="serif">
                    @{{ selected.length }} @{{ selected.length > 1 ? dict.TITLES_USERS : dict.TITLE_USERS | lowercase }} selected
                </span>
                <md-button class="md-raised md-warn float-right" ng-click="removeMany($event)" aria-label="Delete">
                    <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                    <span><strong>Delete @{{ selected.length > 1 ? dict.TITLES_USERS : dict.TITLE_USERS | lowercase }}</strong></span>
                </md-button>
            </md-subheader>
        </md-toolbar>
        <md-table-container>
            <table md-table md-row-select multiple ng-model="selected" md-progress="promise">
                <thead md-head md-order="query.order" md-on-reorder="onOrderChange">
                    <tr md-row>
                        <th md-column md-order-by="username">Username</th>
                        <th md-column md-order-by="email">Email</th>
                        <th md-column>Role</th>
                        <th md-column></th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr md-row md-select="user" md-select-id="id" ng-repeat="user in users">
                        <td md-cell>@{{ user.username }}</td>
                        <td md-cell>@{{ user.email }}</td>
                        <td md-cell>@{{ user.role }}</td>
                        <td md-cell class="force-text-right">
                            {{-- Force the width so the buttons don't stack on mobile --}}
                            <div class="inline-block actions">
                                <md-button class="md-raised md-mini md-primary" ui-sref="admin.users.edit({id: user.id})" ng-disabled="selected.length" aria-label="Edit">
                                    <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                                    <md-tooltip md-direction="top">
                                        Edit
                                    </md-tooltip>
                                </md-button>
                                <md-button class="md-raised md-mini md-warn" ng-click="remove(user, $event)" ng-disabled="selected.length" aria-label="Remove">
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
        <md-table-pagination md-limit="query.limit" md-limit-options="[10, 20, 50]" md-page="query.page" md-total="@{{ users._meta.total }}" md-label="pagination.label" md-on-paginate="onPaginationChange" md-page-select></md-table-pagination>
    </div>
    <md-card class="no-content-panel ng-hide" ng-show="!filter.show && users._meta.total < 2">
        <md-card-title>
            <md-card-title-text>
                <span class="md-headline">
                    <strong>
                        New to {!! env('APP_NAME') !!}?
                    </strong>
                </span>
                <span class="md-subhead">
                    You're currently in the @{{ dict.TITLES_USERS }} section 
                    of {!! env('APP_NAME') !!}, and it looks like you have not 
                    created any @{{ dict.TITLES_USERS | lowercase }} yet.
                    To get started, create a new @{{ dict.TITLE_USERS | lowercase }}
                    to manage @{{ dict.TITLES_LISTS | lowercase }}, 
                    @{{ dict.TITLES_TEMPLATES | lowercase }}, and 
                    @{{ dict.TITLES_EMAILERS | lowercase }}.
                </span>
            </md-card-title-text>
        </md-card-title>
        <md-card-content>
            <small>
                This message will disappear once you have created a 
                @{{ dict.TITLE_USERS | lowercase }}.
            </small>
        </md-card-content>
        <md-card-actions class="text-right">
            <md-button ui-sref="admin.help.index.list.all" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE0C6;</md-icon>
                Help
            </md-button>
            <md-button ui-sref="admin.users.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                Add a @{{ dict.TITLE_USERS }}
            </md-button>
        </md-card-actions>
    </md-card>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only" ng-if="userHasPermission('ADMIN')">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.users.new" class="md-raised md-primary">
                <md-icon class="material-icons">&#xE03B;</md-icon>
                Add {{ env('TITLE_USERS') }}
            </md-button>
        </div>
    </div>
@stop
