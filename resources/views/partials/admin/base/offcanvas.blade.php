@section('offcanvas')
    <nav ng-cloak>
        <ul class="off-canvas-list">
            <li class="label">
                <a ui-sref="admin.index" class="top-bar-client-logo">
                    <span class="show-for-sr"> @{{ dict.TITLE_DASHBOARD }} </span>
                </a>
            </li>
            <li ui-sref-active="{active: 'admin.index'}" class="text-center">
                <md-button ui-sref="admin.index" class="md-raised md-primary">
                    <md-icon md-font-set="material-icons">&#xE8F1;</md-icon> 
                    Go to @{{ dict.TITLE_DASHBOARD }}
                </md-button>
            </li>
            <li ui-sref-active="{active: 'admin.emailers'}">
                <md-button ui-sref="admin.emailers.list" class="md-flat">
                    <md-icon md-font-set="material-icons">&#xE031;</md-icon>
                    @{{ dict.TITLES_EMAILERS }}
                </md-button>
            </li>
            <li ui-sref-active="{active: 'admin.templates'}">
                <md-button ui-sref="admin.templates.list" class="md-flat">
                    <md-icon md-font-set="material-icons">&#xE41D;</md-icon>
                    @{{ dict.TITLES_TEMPLATES }}
                </md-button>
            </li>
            <li ui-sref-active="{active: 'admin.lists'}">
                <md-button ui-sref="admin.lists.list" class="md-flat">
                    <md-icon md-font-set="material-icons">&#xE03F;</md-icon>
                    @{{ dict.TITLES_LISTS }}
                </md-button>
            </li>
            <li ui-sref-active="{active: 'admin.users'}" ng-if="$root.user.role === 'ADMIN'">
                <md-button ui-sref="admin.users.list" class="md-flat">
                    <md-icon md-font-set="material-icons">&#xE851;</md-icon>
                    @{{ dict.TITLES_USERS }}
                </md-button>
            </li>
            <li ui-sref-active="{active: 'admin.help'}">
                <md-button ui-sref="admin.help.index.list.all" class="md-flat">
                    <md-icon md-font-set="material-icons">&#xE0C6;</md-icon>
                    @{{ dict.TITLES_ARTICLES }}
                </md-button>
            </li>
        </ul>
    </nav>
@show
