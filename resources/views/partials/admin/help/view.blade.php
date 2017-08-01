<article class="row help-article-view" ng-if="article.id">
    <div class="columns small-12 small-centered help-article">
        <div class="help-article-inner md-whiteframe-2dp">
            <md-toolbar class="help-article-toolbar">
                <h2 class="md-toolbar-tools text-right">
                    <md-subheader>
                        <md-button class="md-mini md-raised md-accent no-margin" aria-label="Close" ng-click="close()">
                            <md-icon class="material-icons">&#xE5CD;</md-icon>
                            <md-tooltip md-direction="left">
                                Close
                            </md-tooltip>
                        </md-button>
                    </md-subheader>
                </h2>
            </md-toolbar>
            <div class="help-article-title">
                <span>@{{ article.title }}</span>
            </div>
            <div class="help-article-updated" title="@{{ article.updated_at | amUtc | amLocal }}">
                <small>
                    Updated
                    <span>@{{ article.updated_at | amUtc | amLocal | amCalendar:referenceTime:formats }}</span>
                </small>
            </div>
            <md-content class="help-article-content serif">
                <div ng-bind-html="article.content"></div>
            </md-content>
        </div>
    </div>
</article>
