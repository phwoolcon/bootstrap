/*! phwoolcon v1.0-dev | Apache-2.0 */
/* SSO api */
!function (w, d) {
    w.$p || (w.$p = {options: {
        isSsoServer: false,
        ssoCheckUri: "sso/check",
        baseUrl: "/"
    }});
    var $p = w.$p,
        $pOptions = $p.options,
        json = w.JSON,
        console = w.console;
    if (!json) {
        throw new Error("Please include JSON support script to your page.");
    }
    var SSO_URL_CHECK = $pOptions.ssoCheckUri,
        TTL_ONE_DAY = 86400;
    var methodCreateElement = "createElement",
        methodGetElementsByTagName = "getElementsByTagName",
        methodAppendChild = "appendChild",
        methodApply = "apply",
        methodAddEventListener = "addEventListener";
    var propertyClientUid = "clientUid",
        propertySsoServer = "ssoServer",
        propertySsoCheckUri = "ssoCheckUri",
        propertyIsSsoServer = "isSsoServer",
        propertyDebug = "debug",
        propertyInitTime = "initTime";

    function _debug(info) {
        console && options[propertyDebug] && console.log(info);
    }

    function _serverCheck() {
        var clientUid = vars[propertyClientUid],
            serverUid = oThis.getUid(),
            clientWindow = w.parent;
        timerServerCheck = setTimeout(function () {
            _serverCheck[methodApply](oThis);
        }, 1000);
        if (clientUid == serverUid) {
            return;
        }
        _debug("Start checking");
        _debug("Server uid: " + serverUid);
        if (serverUid) {
            // Login
            _debug("Login: " + serverUid);
            var loginData = {uid: serverUid};
            // TODO Fill loginData via ajax request to sso server
            vars[propertyClientUid] = serverUid;
            _sendMsgTo(clientWindow, {login: loginData});
        } else {
            // Logout
            _debug("Logout");
            vars[propertyClientUid] = null;
            _sendMsgTo(clientWindow, {logout: true});
        }
    }

    function _serverOnMessage(e) {
        var data = _getJson(e.data),
            clientUid;
        if (data[propertyDebug]) {
            options[propertyDebug] = true;
        }
        _debug("Handle in iframe");
        if (clientUid = data[propertyClientUid]) {
            _debug("Aware client uid: " + clientUid);
            vars[propertyClientUid] = clientUid;
        }
        if (data.check) {
            _serverCheck[methodApply](oThis);
        }
        if (data.stopCheck) {
            _debug("Stop checking");
            clearTimeout(timerServerCheck);
        }
    }

    function _clientOnMessage(e) {
        var data = _getJson(e.data),
            loginData;
        _debug("Handle in client");
        if (loginData = data.login) {
            _clientLogin(loginData);
        }
        if (data.logout) {
            _clientLogout();
        }
    }

    function _clientLogin(loginData) {
        _debug("Client login");
        _debug(loginData);
        // TODO Invoke client notify url to finish login
        oThis.setUid(loginData.uid);
    }

    function _clientLogout() {
        _debug("Client logout");
        // TODO Invoke client notify url to finish logout
        oThis.setUid(null);
    }

    function _listen(host, eventName, callback) {
        if (methodAddEventListener in host) {
            host[methodAddEventListener](eventName, callback, false);
        } else {
            host.attachEvent("on" + eventName, callback);
        }
    }

    function _sendMsgTo(frame, message) {
        frame.postMessage(typeof message == "string" ? message : _jsonStringify(message), msgTarget);
    }

    function _getJson(data) {
        var jsonData;
        try {
            jsonData = json.parse(data);
            return jsonData;
        } catch (E) {
            return data;
        }
    }

    function _jsonStringify(obj) {
        return json.stringify(obj);
    }

    var options = {
            ssoServer: $pOptions.baseUrl,
            isSsoServer: $pOptions[propertyIsSsoServer],
            siteId: 0,
            ssoToken: "",
            initTime: 0,
            debug: false
        },
        initialized = false,
        ls = w.localStorage,
        ss = w.simpleStorage || {
                get: function (key) {
                    return ls ? ls.getItem(key) : false;
                },
                set: function (key, value) {
                    return ls ? ls.setItem(key, value) : false;
                }
            },
        vars = {
            clientUid: null,
            notifyUrl: null
        },
        iw = false,
        msgTarget = "*",
        timerJsonLoad = 0,
        timerServerCheck = 0,
        $ = w.jQuery;

    var oThis = $p.sso = {
        options: options,
        init: function (ssoOptions) {
            if (ssoOptions) for (var key in ssoOptions) {
                if (ssoOptions.hasOwnProperty(key)) {
                    options[key] = ssoOptions[key];
                }
            }
            if (initialized) {
                return;
            }
            if (options[propertyInitTime] && (new Date) / 1000 > options[propertyInitTime] + TTL_ONE_DAY) {
                w.location.reload();
                return;
            }
            initialized = true;
            if (options[propertyIsSsoServer]) {
                msgTarget = d.referrer;
                _listen(w, "message", function (e) {
                    _serverOnMessage[methodApply](oThis, [e]);
                });
            } else {
                msgTarget = options[propertySsoServer];
                _listen(w, "message", function (e) {
                    _clientOnMessage[methodApply](oThis, [e]);
                });
            }
        },
        setOption: function (key, value) {
            options[key] = value;
            return oThis;
        },
        check: function () {
            if (!initialized) {
                throw new Error("Please invoke $p.sso.init() first.");
            }
            _debug("Check triggered");
            var iframe = d[methodCreateElement]("iframe"),
                clientUid = oThis.getUid();
            _debug("Detected client uid: " + clientUid);
            iframe.src = options[propertySsoServer] + options[propertySsoCheckUri];
            iframe.id = "sso-check-iframe";
            iframe.width = 0;
            iframe.height = 0;
            iframe.frameBorder = 0;
            iframe.style.display = "none";
            d[methodGetElementsByTagName]("body")[0][methodAppendChild](iframe);
            _listen(iframe, "load", function () {
                var attempt = 0;
                iw = iframe.contentWindow;
                _sendMsgTo(iw, {debug: options[propertyDebug], clientUid: clientUid, check: true});
            });
        },
        stopCheck: function () {
            _sendMsgTo(iw, {stopCheck: true});
        },
        getUid: function () {
            return ss && ss.get(options[propertyIsSsoServer] ? "uid" : "_sso_uid");
        },
        setUid: function (uid, ttl) {
            var options = {TTL: ttl || 0};
            _debug("Set uid: " + uid);
            ss && ss.set(options[propertyIsSsoServer] ? "uid" : "_sso_uid", uid, options);
        }
    };

    _listen(w, "load", function () {
        oThis.init();
    });
}(window, document);
