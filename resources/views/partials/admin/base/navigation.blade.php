@section('navigation')
    <div class="navigation navigation-admin top-navigation-admin action-bar top-bar" role="navigation" ng-if="userIsLoggedIn()">
        <div class="action-bar-inner">
            <div class="top-bar-right float-right">
                <ul class="menu top-bar-user-menu">
                    <li ui-sref-active="{active: 'admin.help'}">
                        <a ui-sref="admin.help.index.list.all">
                            <md-button class="md-icon-button" aria-label="@{{ dict.TITLES_ARTICLES }}">
                                <md-icon md-font-set="material-icons">&#xE0C6;</md-icon>
                            </md-button>
                        </a>
                    </li>
                    <li ui-sref-active="{active: 'admin.users'}">
                        <a ui-sref="admin.users.edit({id: $root.user.id})">
                            <md-button class="md-icon-button" aria-label="Edit Profile">
                                <md-icon md-font-set="material-icons">&#xE851;</md-icon>
                            </md-button>
                        </a>
                    </li>
                    <li>
                        <a ui-sref="admin.logout">
                            <md-button class="md-icon-button" aria-label="Sign-out">
                                <md-icon md-font-set="material-icons">&#xE879;</md-icon>
                            </md-button>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="top-bar-left">
                <a ui-sref="admin.index" class="top-bar-client-logo">
                    <md-tooltip md-direction="right">
                        Dashboard
                    </md-tooltip>
                </a>
                <md-button class="md-icon-button hide-for-large top-bar-menu-toggle" aria-label="Menu" ng-click="toggleOffCanvas()">
                    <md-icon md-font-set="material-icons">&#xE5D2;</md-icon>
                </md-button>
                <ul class="menu top-bar-navigation-menu show-for-large">
                    <li ui-sref-active="{active: 'admin.emailers'}">
                        <a ui-sref="admin.emailers.list">
                            <md-button> @{{ dict.TITLES_EMAILERS }} </md-button>
                        </a>
                    </li>
                    <li ui-sref-active="{active: 'admin.templates'}">
                        <a ui-sref="admin.templates.list">
                            <md-button> @{{ dict.TITLES_TEMPLATES }} </md-button>
                        </a>
                    </li>
                    <li ui-sref-active="{active: 'admin.lists'}">
                        <a ui-sref="admin.lists.list">
                            <md-button> @{{ dict.TITLES_LISTS }} </md-button>
                        </a>
                    </li>
                    <li ui-sref-active="{active: 'admin.users'}" ng-if="$root.user.role === 'ADMIN'">
                        <a ui-sref="admin.users.list">
                            <md-button> @{{ dict.TITLES_USERS }} </md-button>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@show
