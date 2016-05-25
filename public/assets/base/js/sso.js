/*! phwoolcon v1.0-dev | Apache-2.0 */
/* SSO api */
!function (w, d) {
    w.$p || (w.$p = {options: {isSsoServer: false, baseUrl: "/"}});
    function _debug(info) {
        w.console && oThis.options.debug && w.console.log(info);
    }

    function _serverCheck() {
        var clientUid = _.vars.clientUid,
            serverUid = oThis.getUid(),
            clientWindow = w.parent;
        _.serverCheckTimer = setTimeout(function () {
            _serverCheck.apply(oThis);
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
            _.vars.clientUid = serverUid;
            _sendMsgTo(clientWindow, {login: loginData});
        } else {
            // Logout
            _debug("Logout");
            _.vars.clientUid = null;
            _sendMsgTo(clientWindow, {logout: true});
        }
    }

    function _serverOnMessage(e) {
        var data = _getJson(e.data),
            clientUid;
        if (data.debug) {
            oThis.options.debug = true;
        }
        _debug("Handle in iframe");
        if (clientUid = data.clientUid) {
            _debug("Aware client uid: " + clientUid);
            _.vars.clientUid = clientUid;
        }
        if (data.check) {
            _serverCheck.apply(oThis);
        }
        if (data.stopCheck) {
            _debug("Stop checking");
            clearTimeout(_.serverCheckTimer);
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
        if ("addEventListener" in host) {
            host.addEventListener(eventName, callback, false);
        } else {
            host.attachEvent("on" + eventName, callback);
        }
    }

    function _sendMsgTo(frame, message) {
        frame.postMessage(typeof message == "string" ? message : _jsonStringify(message), oThis.options.origin);
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

    function _loadJsonSupportScript() {
        if (!json && !_jsonLoaded) {
            _jsonLoaded = true;
            var script = d.createElement("script");
            _listen(script, "readystatechange", function () {
                json = w.JSON;
            });
            d.getElementsByTagName("head")[0].appendChild(script);
            script.src = oThis.options.ssoServer + "assets/base/js/ie/json2-20160501.min.js";
        }
        return json;
    }

    var _initialized = false,
        json = w.JSON,
        _jsonLoaded = false,
        _ls = w.localStorage,
        _ss = w.simpleStorage || {
                get: function (key) {
                    return _ls ? _ls.getItem(key) : false;
                },
                set: function (key, value) {
                    return _ls ? _ls.setItem(key, value) : false;
                }
            },
        _ = {
            vars: {
                clientUid: null,
                notifyUrl: null
            },
            checkIframeUrl: "sso/check-iframe",
            serverCheckTimer: 0,
            jsonLoadTimer: 0,
            iw: false
        },
        $ = w.jQuery;

    var oThis = $p.sso = {
        options: {
            ssoServer: $p.options.baseUrl,
            isSsoServer: $p.options.isSsoServer,
            origin: "*",
            debug: false
        },
        init: function (options) {
            var o = oThis.options;
            if (options) {
                o = oThis.options = options
            }
            if (_initialized === true) {
                return;
            }
            json || _loadJsonSupportScript();
            _initialized = true;
            if (o.isSsoServer) {
                o.origin = d.referrer;
                _listen(w, "message", function (e) {
                    _serverOnMessage.apply(oThis, [e]);
                });
            } else {
                o.origin = o.ssoServer;
                _listen(w, "message", function (e) {
                    _clientOnMessage.apply(oThis, [e]);
                });
            }
        },
        setOption: function (key, value) {
            oThis.options[key] = value;
            return oThis;
        },
        check: function () {
            if (!_initialized) {
                throw new Error("Please invoke $p.sso.init() first.");
            }
            _debug("Check triggered");
            var o = oThis.options,
                iframe = d.createElement("iframe"),
                clientUid = oThis.getUid();
            _debug("Detected client uid: " + clientUid);
            iframe.src = o.ssoServer + _.checkIframeUrl;
            iframe.id = "sso-check-iframe";
            iframe.width = 0;
            iframe.height = 0;
            iframe.frameBorder = 0;
            iframe.style.display = "none";
            d.getElementsByTagName("body")[0].appendChild(iframe);
            _listen(iframe, "load", function () {
                var iw = _.iw = iframe.contentWindow,
                    attempt = 0;
                _.jsonLoadTimer = setInterval(function () {
                    if (json) {
                        clearInterval(_.jsonLoadTimer);
                        _sendMsgTo(iw, {debug: o.debug, clientUid: clientUid, check: true});
                    } else if (++attempt > 100) {
                        clearInterval(_.jsonLoadTimer);
                        throw new Error("Please include JSON support script to your page.");
                    }
                }, 100);
            });
        },
        stopCheck: function () {
            _sendMsgTo(_.iw, {stopCheck: true});
        },
        getUid: function () {
            return _ss && _ss.get(oThis.options.isSsoServer ? "uid" : "_sso_uid");
        },
        setUid: function (uid, ttl) {
            var options = {TTL: ttl || 0};
            _debug("Set uid: " + uid);
            _ss && _ss.set(oThis.options.isSsoServer ? "uid" : "_sso_uid", uid, options);
        }
    };

    _listen(w, "load", function () {
        oThis.init();
    });
}(window, document);
