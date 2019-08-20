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

    for (let obj in this.config) {
        for (let trigger in this.config[obj]) {
            this.fluentObjects[obj].on(trigger, this.config[obj][trigger]);
        }
    }

};
