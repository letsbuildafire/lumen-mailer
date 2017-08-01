(function () {
    "use strict";

    angular.module("thin").config(["$provide", function($provide){
        // Placeholder tags
        $provide.decorator("taOptions", [
            "taRegisterTool",
            "$delegate",
            "taSelection",
            "$document",
            function(
                taRegisterTool,
                taOptions,
                taSelection,
                $document
            ){

                var drop_menu = "<md-menu> \
                    <md-button ng-click='openMenu($mdOpenMenu, $event)' aria-label='Open placeholder menu' class='md-icon-button md-mini md-raised md-primary toolbar-button'> \
                        <md-icon aria-label='Placeholders' md-font-set='material-icons'>&#xE22A;</md-icon> \
                    </md-button> \
                    <md-menu-content width='3'> \
                        <md-menu-item class='ta-menu-item' ng-repeat='f in fields'> \
                            <md-button ng-click='insertPlaceholder(f.name)' ng-class='{req: f.req}'> \
                                {{ f.name }} \
                                <span class='md-caption' ng-show='f.occurs'> \
                                    in {{ f.occurs > 1 ? f.occurs + ' lists' : f.occurs + ' list'}} \
                                </span> \
                            </md-button> \
                        </md-menu-item> \
                    </md-menu-content> \
                </md-menu>";

                // register the tool with textAngular
                taRegisterTool("placeholder", {
                    display: drop_menu,
                    class: "ta-menu",
                    disabled: function(){
                        // Fetch the placeholders from the parent scope
                        var self = this;

                        // Set up our placeholders on load
                        var textAngular = $document[0].querySelector("text-angular");
                        this.fields = angular.element(textAngular).scope().custom_fields;

                        this.$on("custom_fields_changed", function(e, fields){
                            self.fields = fields;
                        });

                        this.isDisabled = function(){
                            return $document[0].querySelector(".ta-scroll-window [contenteditable]") !== $document[0].activeElement;
                        };
                        
                        this.openMenu = function($mdOpenMenu, ev) {
                            // Save the current cursor position since we lose focus!
                            $document[0].querySelector(".ta-scroll-window [contenteditable]").focus();
                            this.sel = rangy.getSelection();

                            var originatorEv = ev;
                            $mdOpenMenu(ev);
                        };

                        this.insertPlaceholder = function(placeholder) {
                            var template = "{{%var%}}";
                            template =  template.replace(/%var%/g, placeholder);
                            
                            // Give focus back to the editor
                            $document[0].querySelector(".ta-scroll-window [contenteditable]").focus();

                            // Set the cursor back to where we had it
                            this.sel.collapseToEnd();

                            this.$editor().wrapSelection("insertHTML", template, true);
                        };

                        return;
                    },
                    action: function(deferred, restoreSelection){
                        return;
                    }
                });

                return taOptions;
        }]);

        $provide.decorator("taOptions", [
            "taRegisterTool",
            "$delegate",
            "taSelection",
            "$document",
            function(
                taRegisterTool,
                taOptions,
                taSelection,
                $document
            ){

                var drop_menu = "<md-menu> \
                        <md-button ng-click='openMenu($mdOpenMenu, $event)' aria-label='Open placeholder menu' class='md-icon-button md-mini md-raised md-primary toolbar-button'> \
                            <md-icon aria-label='Placeholders with Default' md-font-set='material-icons'>&#xE228;</md-icon> \
                        </md-button> \
                        <md-menu-content width='3'> \
                            <md-menu-item class='ta-menu-item' ng-repeat='f in fields'> \
                                <md-button ng-click='insertPlaceholderWithDefault(f.name)' ng-class='{req: f.req}'> \
                                    {{ f.name }} \
                                    <span class='md-caption' ng-show='f.occurs'> \
                                        in {{ f.occurs > 1 ? f.occurs + ' lists' : f.occurs + ' list'}} \
                                    </span> \
                                </md-button> \
                            </md-menu-item> \
                        </md-menu-content> \
                    </md-menu>";

                // register the tool with textAngular
                taRegisterTool("placeholderWithDefault", {
                    display: drop_menu,
                    class: "ta-menu",
                    disabled: function(){
                        // Fetch the placeholders from the parent scope
                        var self = this;

                        // Set up our placeholders on load
                        var textAngular = $document[0].querySelector("text-angular");
                        this.fields = angular.element(textAngular).scope().custom_fields;

                        this.$on("custom_fields_changed", function(e, fields){
                            self.fields = fields;
                        });

                        this.isDisabled = function(){
                            return $document[0].querySelector(".ta-scroll-window [contenteditable]") !== $document[0].activeElement;
                        };
                        
                        this.openMenu = function($mdOpenMenu, ev) {
                            // Save the current cursor position since we lose focus!
                            $document[0].querySelector(".ta-scroll-window [contenteditable]").focus();
                            this.sel = rangy.getSelection();

                            var originatorEv = ev;
                            $mdOpenMenu(ev);
                        };

                        this.insertPlaceholderWithDefault = function(placeholder) {
                            var template = "{{%var%|default('%var%',true)}}";
                            template =  template.replace(/%var%/g, placeholder);
                            
                            // Give focus back to the editor
                            $document[0].querySelector(".ta-scroll-window [contenteditable]").focus();

                            // Set the cursor back to where we had it
                            this.sel.collapseToEnd();

                            this.$editor().wrapSelection("insertHTML", template, true);
                        };

                        return;
                    },
                    action: function(deferred, restoreSelection){
                        return;
                    }
                });

                return taOptions;
        }]);

        $provide.decorator("taOptions", [
            "taRegisterTool",
            "$delegate",
            "taSelection",
            "$document",
            function(
                taRegisterTool,
                taOptions,
                taSelection,
                $document
            ){

                var drop_menu = "<md-menu> \
                        <md-button ng-click='openMenu($mdOpenMenu, $event)' aria-label='Open Format Menu' class='md-icon-button md-mini md-raised md-primary toolbar-button'> \
                            <md-icon aria-label='Format' md-font-set='material-icons'>&#xE262;</md-icon> \
                        </md-button> \
                        <md-menu-content width='2'> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"h1\")'> \
                                    H1 \
                                </md-button> \
                            </md-menu-item> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"h2\")'> \
                                    H2 \
                                </md-button> \
                            </md-menu-item> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"h3\")'> \
                                    H3 \
                                </md-button> \
                            </md-menu-item> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"h4\")'> \
                                    H4 \
                                </md-button> \
                            </md-menu-item> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"h5\")'> \
                                    H5 \
                                </md-button> \
                            </md-menu-item> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"h6\")'> \
                                    H6 \
                                </md-button> \
                            </md-menu-item> \
                            <md-menu-item> \
                                <md-button ng-click='headingAction(\"p\")'> \
                                    P \
                                </md-button> \
                            </md-menu-item> \
                        </md-menu-content> \
                    </md-menu>";

                // register the tool with textAngular
                taRegisterTool("formatSelect", {
                    display: drop_menu,
                    class: "ta-menu",
                    disabled: function() {
                        this.isDisabled = function() {
                            return $document[0].querySelector(".ta-scroll-window [contenteditable]") !== $document[0].activeElement;
                        };

                        this.openMenu = function($mdOpenMenu, ev) {
                            // Save the current cursor position since we lose focus!
                            this.sel = rangy.getSelection();

                            var originatorEv = ev;
                            $mdOpenMenu(ev);
                        };

                        this.headingAction = function(level) {
                            // Give focus back to the editor
                            $document[0].querySelector(".ta-scroll-window [contenteditable]").focus();

                            // Set the cursor back to where we had it
                            this.sel.collapseToEnd();
                            if(this.isDisabled()) return false;
                            
                            this.$editor().wrapSelection("formatBlock", "<" + level.toUpperCase() +">", true);
                        };
                        return;
                    },
                    action: function(deferred, restoreSelection){
                        return;
                    }
                });

                return taOptions;
        }]);
    }]);
})();
