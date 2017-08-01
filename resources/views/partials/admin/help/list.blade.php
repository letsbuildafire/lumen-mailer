@extends('partials.admin.layouts.header-content-footer')

@section('header')
    <div class="top-bar action-bar">
        <div class="row collapse action-bar-inner top-bar-inner">
            <div class="top-bar-left">
                <h5 class="text-center medium-text-left">
                    @{{ dict.TITLES_ARTICLES }}
                </h5>
            </div>
            <div class="top-bar-right">
                <md-button ng-hide="selected.length || filter.show" class="md-mini md-raised md-primary" ng-click="filter.show = true">
                    <md-icon class="material-icons">&#xE8B6;</md-icon>
                    <md-tooltip md-direction="left">
                        Search
                    </md-tooltip>
                </md-button>
                <md-button aria-hidden="true" ng-show="filter.show && !selected.length" class="md-mini md-raised md-accent" ng-click="resetFilter()">
                    <md-icon class="material-icons">&#xE14C;</md-icon>
                </md-button>
                <md-button ui-sref="admin.help.new" class="md-raised md-primary" ng-if="userHasPermission('ADMIN')">
                    <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                    Add @{{ dict.TITLE_ARTICLES }}
                </md-button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="la-tabs la-help-sections" ng-class="{'can-add-articles': userHasPermission('ADMIN')}">
        <md-toolbar class="md-filter-toolbar ng-hide" ng-show="filter.show && !selected.length">
            <div class="top-bar-filters float-none">
                <form name="filterForm" class="filter-form medium-text-left">
                    <md-input-container class="help-article-search">
                        <label>Search by Title</label>
                        <input ng-model="query.q" ng-model-options="filter.options" type="text">
                        <md-icon class="material-icons">&#xE8B6;</md-icon>
                    </md-input-container>
                </form>
            </div>
        </md-toolbar>
        <md-tabs md-enable-disconnect md-selected="selectedIndex" md-dynamic-height md-border-bottom>
            <md-tab ng-repeat="section in sections" label="@{{ section.label | titlecase }}">
                <md-list class="la-list la-list-simple la-help-section-list">
                    <md-list-item ng-if="!articles.length" class="md-3-line" >
                        <div class="md-list-item-text help-article">
                            <div class="no-result">
                                <h3 class="help-article-title">No @{{ dict.TITLES_ARTICLES | lowercase }} found</h3>
                                <p ng-hide="!query.q.length">
                                    No @{{ dict.TITLES_ARTICLES | lowercase }} were found in this category using those search terms. <br>
                                    <br>
                                    <md-button class="md-raised md-primary" ng-click="resetFilter($event)" aria-label="Reset Search">
                                        <md-icon md-font-set="material-icons">&#xE8B3;</md-icon>
                                        Reset
                                    </md-button>
                                </p>
                                <p class="ng-hide" ng-show="!query.q.length">
                                    No @{{ dict.TITLES_ARTICLES | lowercase }} were found in this category.
                                </p>
                            </div>
                        </div>
                    </md-list-item>
                    <md-list-item ng-repeat="article in articles" ng-click="view(article, $event)" class="md-3-line" ng-class="{'active': selected === article.id}">
                        <div class="md-list-item-text help-article">
                            <h3 class="help-article-title">@{{ article.title }}</h3>
                            <h3 class="help-article-updated">
                                <small title="@{{ article.updated_at | amUtc | amLocal }}">
                                    Updated
                                    <time am-time-ago="article.updated_at | amUtc | amLocal"></time>
                                </small></h3>
                            <p class="help-article-content serif">
                                @{{ article.content | sanitize | limitTo:240 }}...
                            </p>
                            <div class="md-secondary">
                                <md-button ng-if="userHasPermission('ADMIN')" class="md-raised md-mini md-warn float-right" ng-click="remove(article, $event)" aria-label="Remove">
                                    <md-icon md-font-set="material-icons">&#xE92B;</md-icon>
                                    <md-tooltip md-direction="top">
                                        Remove
                                    </md-tooltip>
                                </md-button>
                                <md-button ng-if="userHasPermission('ADMIN')" class="md-raised md-mini md-primary float-right" ng-click="edit(article, $event)" aria-label="Edit">
                                    <md-icon md-font-set="material-icons">&#xE150;</md-icon>
                                    <md-tooltip md-direction="top">
                                        Edit
                                    </md-tooltip>
                                </md-button>
                            </div>
                        </div>
                    </md-list-item>
                </md-list>
            </md-tab>
        </md-tabs>
    </div>
@stop

@section('footer')
    <div class="row bottom-bar action-bar show-for-small-only" ng-if="userHasPermission('ADMIN')">
        <div class="columns small-12 action-bar-inner bottom-bar-inner text-center medium-text-right">
            <md-button ui-sref="admin.help.new" class="md-raised md-primary">
                <md-icon md-font-set="material-icons">&#xE03B;</md-icon>
                    Add @{{ dict.TITLE_ARTICLES }}
                </md-button>
        </div>
    </div>
@stop
