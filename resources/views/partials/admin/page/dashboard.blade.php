<section class="content admin-dashboard">
    <div class="row dashboard-panels">
        <div class="columns small-12 large-9">
            <div class="columns small-12">
                <div class="dashboard-intro">
                    <div class="dashboard-intro-header">
                        <h1>Dashboard</h1>
                        <h3 class="serif">
                            <em>Don't know where to start?</em>
                        </h3>
                    </div>
                    <md-divider></md-divider>
                    <md-list class="la-list la-list-simple la-dashboard-intro-list">
                        <md-list-item disabled class="md-2-line">
                            <div class="md-list-item-text">
                                <div class="list-icon add-list-icon"></div>
                                <div class="columns small-12 medium-8">
                                    <h3>
                                        Create a @{{ dict.TITLE_LISTS | lowercase }}
                                        <md-icon class="material-icons colour-success ng-hide" ng-show="lists.length">&#xE86C;</md-icon>
                                    </h3>
                                    <span class="serif">
                                        @{{ dict.TITLES_LISTS }} are where you store your contacts 
                                        (we call them @{{ dict.TITLES_ADDRESSES | lowercase }}). 
                                        Create @{{ dict.TITLES_LISTS | lowercase }} for 
                                        different segments and demographics to send
                                        the right message to the right people.
                                    </span>
                                </div>
                                <div class="columns small-12 medium-4">
                                    <md-button class="md-raised md-primary float-right ng-hide" md-theme="extended" ng-show="lists.length" ui-sref="admin.lists.list">
                                        View @{{ dict.TITLES_LISTS }}
                                    </md-button>
                                    <md-button class="md-raised md-primary float-right" ng-hide="lists.length" ui-sref="admin.lists.new">
                                        Create a @{{ dict.TITLE_LISTS }}
                                    </md-button>
                                </div>
                            </div>
                        </md-list-item>
                        <md-list-item disabled class="md-2-line">
                            <div class="md-list-item-text">
                                <div class="list-icon add-subscriber-icon"></div>
                                <div class="columns small-12 medium-8">
                                    <h3>
                                        Start building your audience
                                        <md-icon class="material-icons colour-success ng-hide" ng-show="lists.length">&#xE86C;</md-icon>
                                    </h3>
                                    <span class="serif">
                                        When you create a @{{ dict.TITLE_LISTS | lowercase }}, 
                                        you'll then have the option to import 
                                        @{{ dict.TITLE_ADDRESSES | lowercase }} data
                                        from standard CSV files or manual input.</span>
                                </div>
                                <div class="columns small-12 medium-4">
                                    <md-button class="md-raised md-primary float-right ng-hide" md-theme="extended" ng-show="lists.length" ui-sref="admin.lists.list">
                                        View @{{ dict.TITLES_LISTS }}
                                    </md-button>
                                    <md-button class="md-raised md-primary float-right" ng-hide="lists.length" ui-sref="admin.lists.new">
                                        Create a @{{ dict.TITLE_LISTS }}
                                    </md-button>
                                </div>
                            </div>
                        </md-list-item>
                        <md-list-item disabled class="md-2-line">
                            <div class="md-list-item-text">
                                <div class="list-icon add-emailer-icon"></div>
                                <div class="columns small-12 medium-8">
                                    <h3>
                                        Create and send a @{{ dict.TITLE_EMAILERS | lowercase }}
                                        <md-icon class="material-icons colour-success ng-hide" ng-show="emailers.length">&#xE86C;</md-icon>
                                    </h3>
                                    <span class="serif">
                                        @{{ dict.TITLES_EMAILERS }} are emails sent to 
                                        @{{ dict.TITLES_ADDRESSES | lowercase }} in a 
                                        @{{ dict.TITLE_LISTS | lowercase }}. Try your hand 
                                        at communicating your brand by creating and sending 
                                        a test @{{ dict.TITLE_EMAILERS | lowercase }}.
                                    </span>
                                </div>
                                <div class="columns small-12 medium-4">
                                    <md-button class="md-raised md-primary float-right ng-hide" ng-show="lists.length" ui-sref="admin.emailers.new">
                                        Create a @{{ dict.TITLE_EMAILERS }}
                                    </md-button>
                                    <md-button class="md-raised md-primary float-right" ng-hide="lists.length" ui-sref="admin.lists.new">
                                        Create a @{{ dict.TITLE_LISTS }}
                                    </md-button>
                                </div>
                            </div>
                        </md-list-item>
                    </md-list>
                    <md-divider></md-divider>
                </div>
            </div>
        </div>
        <div class="columns small-12 large-3">
            <div class="columns small-12">
                <md-card class="dashboard-panel dashboard-panel-help blue">
                    <md-card-title>
                        <md-card-title-text>
                            <md-icon class="material-icons">&#xE887;</md-icon>
                            <a ui-sref="admin.help.index.list.all" class="md-headline">
                                @{{ dict.TITLES_ARTICLES }}
                            </a>
                        </md-card-title-text>
                        <md-card-actions class="text-right">
                            <md-button ui-sref="admin.help.index.list.all" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE895;</md-icon>
                                <md-tooltip md-direction="left">
                                    Browse
                                </md-tooltip>
                            </md-button>
                        </md-card-actions>
                    </md-card-title>
                </md-card>
            </div>
            <div class="columns small-12 medium-6 large-12">
                <md-card class="dashboard-panel dashboard-panel-emailers purple">
                    <md-card-title>
                        <md-card-title-text>
                            <md-icon class="material-icons">&#xE031;</md-icon>
                            <a ui-sref="admin.emailers.list" class="md-headline"> 
                                @{{ dict.TITLES_EMAILERS }}
                            </a>
                            <span class="md-subhead" ng-hide="emailers.length">
                                No Recent Updates
                            </span>
                            <span class="md-subhead ng-hide" ng-show="emailers.length">
                                Recently Updated
                            </span>
                        </md-card-title-text>
                        <md-card-actions class="text-right">
                            <md-button aria-label="Add New" ui-sref="admin.emailers.new" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE03B;</md-icon>
                                <md-tooltip md-direction="top">
                                    Add New
                                </md-tooltip>
                            </md-button>
                            <md-button ui-sref="admin.emailers.list" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE895;</md-icon>
                                <md-tooltip md-direction="top">
                                    Browse
                                </md-tooltip>
                            </md-button>
                        </md-card-actions>
                    </md-card-title>
                    <md-card-content>
                        <md-list>
                            <md-list-item ng-disabled="$root.user.role !== 'ADMIN'" ng-click="view($event, 'admin.emailers.edit', emailer.id)" class="secondary-button-padding" ng-repeat="emailer in emailers">
                                <div class="md-list-item-text">
                                    <small class="block">
                                        <strong>@{{ emailer.subject }}</strong>
                                    </small>
                                    <small class="block">
                                        Status
                                        <span class="md-card-accent">
                                            <strong>@{{ emailer.status | titlecase }}</strong>
                                        </span>
                                        <span class="float-right">
                                            <time am-time-ago="emailer.updated_at | amUtc | amLocal" title="@{{ emailer.updated_at | amUtc | amLocal }}"></time>
                                        </span>
                                    </small>
                                </div>
                                <md-divider ng-hide="$last"></md-divider>
                            </md-list-item>
                        </md-list>
                    </md-card-content>
                </md-card>
            </div>
            <div class="columns small-12 medium-6 large-12">
                <md-card class="dashboard-panel dashboard-panel-lists green">
                    <md-card-title>
                        <md-card-title-text>
                            <md-icon class="material-icons">&#xE03F;</md-icon>
                            <a ui-sref="admin.lists.list" class="md-headline">
                                @{{ dict.TITLES_LISTS }}
                            </a>
                            <span class="md-subhead" ng-hide="lists.length">
                                No Recent Updates
                            </span>
                            <span class="md-subhead ng-hide" ng-show="lists.length">
                                Recently Updated
                            </span>
                        </md-card-title-text>
                        <md-card-actions class="text-right">
                            <md-button aria-label="Add New" ui-sref="admin.lists.new" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE03B;</md-icon>
                                <md-tooltip md-direction="top">
                                    Add New
                                </md-tooltip>
                            </md-button>
                            <md-button ui-sref="admin.lists.list" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE895;</md-icon>
                                <md-tooltip md-direction="top">
                                    Browse
                                </md-tooltip>
                            </md-button>
                        </md-card-actions>
                    </md-card-title>
                    <md-card-content>
                        <md-list>
                            <md-list-item ng-disabled="$root.user.role !== 'ADMIN'" ng-click="view($event, 'admin.lists.edit', list.id)" class="secondary-button-padding" ng-repeat="list in lists">
                                <div class="md-list-item-text">
                                    <small>
                                        <strong>@{{ list.name }}</strong>
                                        <span class="float-right">
                                            <time am-time-ago="list.updated_at | amUtc | amLocal" title="@{{ list.updated_at | amUtc | amLocal }}"></time>
                                        </span>
                                    </small>
                                </div>
                                <md-divider ng-hide="$last"></md-divider>
                            </md-list-item>
                        </md-list>
                    </md-card-content>
                </md-card>
            </div>
            <div class="columns small-12 medium-6 large-12">
                <md-card class="dashboard-panel dashboard-panel-templates orange">
                    <md-card-title>
                        <md-card-title-text>
                            <md-icon class="material-icons">&#xE41D;</md-icon>
                            <a ui-sref="admin.templates.list" class="md-headline">
                                @{{ dict.TITLES_TEMPLATES }}
                            </a>
                            <span class="md-subhead" ng-hide="templates.length">
                                No Recent Updates
                            </span>
                            <span class="md-subhead ng-hide" ng-show="templates.length">
                                Recently Updated
                            </span>
                        </md-card-title-text>
                        <md-card-actions class="text-right">
                            <md-button aria-label="Add New" ng-if="$root.user.role === 'ADMIN'" ui-sref="admin.templates.new" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE03B;</md-icon>
                                <md-tooltip md-direction="top">
                                    Add New
                                </md-tooltip>
                            </md-button>
                            <md-button ui-sref="admin.templates.list" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE895;</md-icon>
                                <md-tooltip md-direction="top">
                                    Browse
                                </md-tooltip>
                            </md-button>
                        </md-card-actions>
                    </md-card-title>
                    <md-card-content>
                        <md-list>
                            <md-list-item ng-disabled="$root.user.role !== 'ADMIN'" ng-click="view($event, 'admin.templates.edit', template.id)" class="secondary-button-padding" ng-repeat="template in templates">
                                <div class="md-list-item-text">
                                    <small>
                                        <strong>@{{ template.name }}</strong>
                                        <span class="float-right">
                                            <time am-time-ago="template.updated_at | amUtc | amLocal" title="@{{ template.updated_at | amUtc | amLocal }}"></time>
                                        </span>
                                    </small>
                                </div>
                                <md-divider ng-hide="$last"></md-divider>
                            </md-list-item>
                        </md-list>
                    </md-card-content>
                </md-card>
            </div>
            <div class="columns small-12 medium-6 large-12" ng-if="$root.user.role === 'ADMIN'">
                <md-card class="dashboard-panel dashboard-panel-users blue">
                    <md-card-title>
                        <md-card-title-text>
                            <md-icon class="material-icons">&#xE851;</md-icon>
                            <a ui-sref="admin.users.list" class="md-headline">
                                @{{ dict.TITLES_USERS }}
                            </a>
                            <span class="md-subhead" ng-hide="users.length">
                                No Recent Updates
                            </span>
                            <span class="md-subhead ng-hide" ng-show="users.length">
                                Recently Updated
                            </span>
                        </md-card-title-text>
                        <md-card-actions class="text-right">
                            <md-button aria-label="Add New" ui-sref="admin.users.new" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE03B;</md-icon>
                                <md-tooltip md-direction="top">
                                    Add New
                                </md-tooltip>
                            </md-button>
                            <md-button ui-sref="admin.users.list" class="md-mini md-primary">
                                <md-icon class="material-icons">&#xE895;</md-icon>
                                <md-tooltip md-direction="top">
                                    Browse
                                </md-tooltip>
                            </md-button>
                        </md-card-actions>
                    </md-card-title>
                    <md-card-content>
                        <md-list>
                            <md-list-item ng-disabled="$root.user.role !== 'ADMIN'" ng-click="view($event, 'admin.users.edit', user.id)" class="secondary-button-padding" ng-repeat="user in users">
                                <div class="md-list-item-text">
                                    <small>
                                        <strong>@{{ user.email }}</strong> (@{{ user.username }})
                                        <span class="float-right">
                                            <time am-time-ago="user.updated_at | amUtc | amLocal" title="@{{ user.updated_at | amUtc | amLocal }}"></time>
                                        </span>
                                    </small>
                                </div>
                                <md-divider ng-hide="$last"></md-divider>
                            </md-list-item>
                        </md-list>
                    </md-card-content>
                </md-card>
            </div>
        </div>
    </div>
</section>
