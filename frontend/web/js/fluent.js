var FluentUI = function(config) {

    this.config = undefined;

    this.fluentObjects = {};

    if (config !== undefined) {
        this.constructor(config);
    }

};

FluentUI.prototype.constructor = function(config) {

    this.config = config;

    for (let obj in config) {
        this.fluentObjects[obj] = $(obj);
    }

    this.createHandlers();

};

FluentUI.prototype.createHandlers = function() {

    var fluentUi = this;

    for (let obj in this.config) {
        for (let trigger in this.config[obj]) {
            this.fluentObjects[obj].on(trigger, function() {
                for (let target in fluentUi.config[obj][trigger]) {
                    let element = $(target);
                    for (let action in fluentUi.config[obj][trigger][target]) {
                        element[action](fluentUi.config[obj][trigger][target][action]);
                    }
                }
            });
        }
    }

};
