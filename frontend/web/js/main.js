class SmartSelect {

    constructor(config) {
        this.options   = null;
        this.mainLogic = null;
        this.selectsId = null;

        this.applyConfig(config);

        this.logicSwitch = {};
        this.logicSwitch['logic'] = this.mainLogic;

        var smartSelect = this;
        for (let i = 0; i < this.selectsId.length - 1; ++i) {
            var targetId    = this.selectsId[i + 1];
            this.logicSwitch[this.selectsId[i]] = function() {
                smartSelect.createOptions(this['logic'], targetId);
            };
        }

        this.selectsId = this.selectsId.slice(0, this.selectsId.length - 1);
    }

    init() {
        let select = document.getElementById(this.selectsId[0]);
        let event  = new Event('change');
        select.dispatchEvent(event);
    }

    applyConfig(config) {
        for (let key in this) {
            if (key in config) {
                this[key] = config[key];
            }
        }
    }

    createChangeHandler() {
        var smartSelect = this;
        for (let id of smartSelect.selectsId) {
            let select = document.getElementById(id);
            select.onchange = function() {
                smartSelect.changeSelect(select);
            };
        }
    }

    getCurrentLogic(targetId) {
        let logic = this.mainLogic;
        for (let id of this.selectsId) {
            logic = logic[document.getElementById(id).value];
            if (id == targetId) {
                break;
            }
        }
        return logic;
    }

    changeSelect(field, logic = 0) {
        let method = field.id;
        if (method in this.logicSwitch) {
            if (!logic) {
                logic = this.getCurrentLogic(field.id);
            }
            this.logicSwitch['logic'] = logic;
            this.logicSwitch[method]();
        }
    }

    createOptions(logic, id) {
        let select = document.getElementById(id);
        select.innerHTML = null;
        for (let i in logic) {
            select.appendChild(this.createOption(i, logic[i]));
        }
        this.changeSelect(select, logic[select.value]);
    }

    createOption(value, title) {
        let option = document.createElement('option');
        option.text = title;
        option.value = value;
        return option;
    }

}
