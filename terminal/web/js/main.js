class SmartSelect {

    constructor(config) {
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

class RememberFields {

    /*constructor(fields = null) {
        if (!fields) {
            var rememberFields = this;
            $(document).bind('MemberFields', function() {
                rememberFields.init();
            });
            this.fields = $('input[name]:visible, select[name]:visible');
            $(document).trigger('MemberFields');
        } else {
            this.fields = fields;
        }
    }

    init() {
        if (!('memberFields' in localStorage)) {
            localStorage['memberFields'] = {};
        }
        this.fields.each(function() {
            if (this.name in localStorage['memberFields']) {
                this.value = localStorage['memberFields'][this.name];
            }
            $(this).bind('blur', function() {
                localStorage['memberFields'][this.name] = this.value;
            });
        });
    }*/

}

function convertURLDataToJSON() {
    return '{"' + decodeURI(location.search.substring(1)).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}';
}

function createRequestTable(action) {
    var data = getRequestData(action);
    var link = getURL('/request/request-table/index', data);
    var requestTableArea = $('#main_request_table');
    var iframe = document.createElement('iframe');
    iframe.src = link;
    iframe.classList = 'embed-responsive-item';
    requestTableArea.html(null);
    requestTableArea.append(iframe);
}

function saveRequest(formId) {
    let dataURL = JSON.parse(convertURLDataToJSON());
    let inputs = $('#' + formId + ' input');
    var fields = {};
    inputs.each(function() {
        fields[this.name] = this.value;
    });
    $.ajax({
        type: "POST",
        url: "/request/request-table/index",
        dataType: "html",
        cache: false,
        data: {
            'fields' : fields,
            'serviceObjectId' : dataURL['serviceObjectId'],
            'contractCode' : dataURL['contractCode'],
            'contractTypeId' : dataURL['contractTypeId'],
            'action' : 'request-table',
        },
        success: function(data) {
            location.reload();
        }
    });
}

function getURL(path, data = null) {
    let dataURL = [];
    for (let key in data) {
        dataURL.push(key + '=' + data[key]);
    }
    dataURL = dataURL.length ? '?' + dataURL.join('&') : '';
    return path + dataURL;
}

function getRequestData(action) {
    let dataURL = JSON.parse(convertURLDataToJSON());
    return {
        'layout' : 'iframe',
        'serviceObjectId' : $('#service_object_name').val(),
        'contractCode' : $('#contract_code').val(),
        'contractTypeId' : dataURL['contractTypeId'],
        'action' : action,
    };
}

/*$.event.special.navbarresize = {
    setup: function() {

    },
    submenu : $("#list_for_navbar"),
    navbar  : $("#navbar"),

};*/

function init() {
    var tooltips = $('[data-toggle=tooltip]');
    var popovers = $('[data-toggle=popover]');

    // Popover enable
    tooltips.tooltip();
    popovers.popover().click(function(e) {e.preventDefault()});

    /*$('[data-popover-target]').each(function() {
        let popover = $(this.attributes['data-popover-target'].value);
        $(this).popover({
            //'container' : 'body',
            'html'      : true,
            'content'   : popover.html(),
            'trigger'   : 'hover focus',
            'title'     : 'title',
        });
    });*/

    //$('[data-target="#profile_popover"]').popover('show');
};

$().ready(function() {

    init();

});