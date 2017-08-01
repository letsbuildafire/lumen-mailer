@extends('partials.admin.layouts.header-content-footer')

@section('header')
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    @{{ dict.TITLES_TEMPLATES }}
                </h5>
            </div>
            <div class="top-bar-right" ng-if="userHasPermission('ADMIN')">
                <md-button ng-hide="selected.length || filter.show" class="md-mini md-raised md-primary" ng-click="filter.show = true">
                    <md-icon class="material-icons">&#xE152;</md-icon>
                    <md-tooltip md-direction="left">
                        Filter
                    </md-tooltip>
                </md-button>
                <md-button aria-hidden="true" ng-show="filter.show && !selected.length" class="md-mini md-raised md-accent" ng-click="resetFilter()">
                    <md-icon class="material-icons">&#xE14C;</md-icon>
                </md-button>
                <md-button ng-if="userHasPermission('ADMIN')" ui-sref="admin.templates.new" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                    Add @{{ dict.TITLE_TEMPLATES }}
                </md-button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="la-list la-list-grid la-templates" ng-hide="!filter.show && !templates._meta.total">
        <md-toolbar class="md-filter-toolbar ng-hide" ng-show="filter.show">
            <div class="top-bar-filters float-none">
                <form name="filterForm" class="filter-form medium-text-left">
                    <md-input-container>
                        <label>Search by Name</label>
                        <md-icon class="material-icons">&#xE8B6;</md-icon>
                        <input ng-model="query.q" name="q" ng-model-options="filter.options" type="text" >
                    </md-input-container>
                </form>
            </div>
        </md-toolbar>
        <md-table-container>
            <table md-table md-progress="promise">
                <thead md-head md-order="query.order" md-on-reorder="onOrderChange">
                    <tr md-row>
                        <th md-column md-order-by="name">Name</th>
                        <th md-column md-order-by="created_at">Added</th>
                        <th md-column md-order-by="updated_at">Updated</th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr md-row ng-repeat="template in templates" class="columns small-6 medium-4 large-3">
                        <td md-cell class="template-preview">
                            <iframe ng-src="@{{ '/templates/' + template.source }}" sandbox="allow-same-origin" frameborder="0"></iframe>
                        </td>
                        <td md-cell class="template-details">
                            <div class="template-name">@{{ template.name }}</div>
                            <div class="template-actions">
                                <md-button ng-click="preview(template, $event)" class="md-primary">
                                    <md-icon md-font-set="material-icons">&#xE8F4;</md-icon>
                                    Preview
                                </md-button>
                                <md-button ui-sref="admin.emailers.new({'template': template.id})" class="md-primary">
                                    <md-icon md-font-set="material-icons">&#xE5C8;</md-icon>
                                    Use This
                                </md-button>
                                <div class="template-actions-admin">
                                    <md-button ng-if="userHasPermission('ADMIN')" ui-sref="admin.templates.edit({id: template.id})" class="md-primary">
                                        <md-icon md-font-set="material-icons">&#xE3C9;</md-icon>
                                        Edit
                                    </md-button>
                                    <md-button aria-label="Remove" ng-if="userHasPermission('ADMIN')" ng-click="remove(template, $event)" class="md-raised md-mini md-warn">
                                        <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                    </md-button>
                                </div>
                            </div>
                            <div class="template-created timestamp" ng-show="query.order.indexOf('created_at') !== -1">Added: @{{ dateToUnix(template.created_at) | date:"dd-MM-yyyy" }}</div>
                            <div class="template-updated timestamp" ng-show="query.order.indexOf('updated_at') !== -1">Updated: @{{ dateToUnix(template.updated_at) | date:"dd-MM-yyyy" }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </md-table-container>
        <md-table-pagination md-limit="query.limit" md-limit-options="[12]" md-page="query.page" md-total="@{{ templates._meta.total }}" md-on-paginate="onPaginationChange" md-page-select></md-table-pagination>
    </div>
    <md-card class="no-content-panel ng-hide" ng-show="!filter.show && !templates._meta.total">
        <md-card-title>
            <md-card-title-text>
                <span class="md-headline">
                    <strong>
                        New to {!! env('APP_NAME') !!}?
                    </strong>
                </span>
                <span class="md-subhead">
                    You're currently in the @{{ dict.TITLES_TEMPLATES }} section 
                    of {!! env('APP_NAME') !!}, and it looks like you have not 
                    added any @{{ dict.TITLES_TEMPLATES | lowercase }} yet.
                    To get started, add a new @{{ dict.TITLE_TEMPLATES | lowercase }}
                    to use when sending a @{{ dict.TITLE_EMAILERS | lowercase }}.
                </span>
            </md-card-title-text>
        </md-card-title>
        <md-card-content>
            <small>
                This message will disappear once you have added a 
                @{{ dict.TITLE_TEMPLATES | lowercase }}.
            </small>
        </md-card-content>
        <md-card-actions class="text-right">
            <md-button ui-sref="admin.help.index.list.all" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE0C6;</md-icon>
                Help
            </md-button>
            <md-button ui-sref="admin.templates.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                Add a @{{ dict.TITLE_TEMPLATES }}
            </md-button>
        </md-card-actions>
    </md-card>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only" ng-if="userHasPermission('ADMIN')">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.templates.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                Add @{{ dict.TITLE_TEMPLATES }}
            </md-button>
        </div>
    </div>
@stop

@section('modal')
    <div class="modal modal-template-preview" ng-if="selected.id" ng-click="hide()">
        <div class="columns small-centered text-center template-preview-frame">
            <div class="top-bar template-preview-bar">
                <div class="top-bar-left">
                    <h4>@{{ selected.name }}</h4>
                </div>
                <div class="top-bar-right medium-text-right">
                    <md-button aria-label="Use Template" ui-sref="admin.emailers.new({'template': selected.id})" class="md-primary">
                        <md-icon md-font-set="material-icons">&#xE5C8;</md-icon>
                        Use This
                    </md-button>
                    <md-button aria-label="Edit" ng-if="userHasPermission('ADMIN')" ui-sref="admin.templates.edit({id: selected.id})" class="md-primary">
                        <md-icon md-font-set="material-icons">&#xE3C9;</md-icon>
                        Edit
                    </md-button>
                    <md-button aria-label="Close" ng-click="hide()" class="md-mini md-raised md-accent">
                        <md-icon class="material-icons">&#xE5CD;</md-icon>
                        <md-tooltip md-direction="top">
                            Close
                        </md-tooltip>
                    </md-button>
                </div>
            </div>
            <iframe ng-src="@{{ '/templates/' + selected.source }}" sandbox="allow-same-origin" frameborder="0"></iframe>
        </div>
    </div>
@stop
