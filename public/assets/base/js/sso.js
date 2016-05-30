/*! phwoolcon v1.0-dev | Apache-2.0 */
/* SSO api */
!function (w, d) {
    w.$p || (w.$p = {
        options: {
            ssoCheckUri: "sso/check",
            baseUrl: "/"
        }
    });

    var $ = w.jQuery;
    var SSO_URL_CHECK = $p.options.ssoCheckUri,
        TTL_ONE_DAY = 86400;
    var initialized, iframe, clientWindow, serverWindow, msgTargetOrigin, timerServerCheck;
    var options = {
            ssoServer: $p.options.baseUrl,
            siteId: 0,
            ssoToken: "",
            initTime: 0,
            debug: false
        },
        vars = {};
    var simpleStorage = w.simpleStorage || {
            get: function (key) {
                return w.localStorage ? _getJson(w.localStorage.getItem("_sso_" + key)) : false;
            },
            set: function (key, value) {
                return w.localStorage ? w.localStorage.setItem("_sso_" + key, _jsonStringify(value)) : false;
            }
        };
    var sso = w.$p.sso = {
        options: options,
        init: function (ssoOptions) {
            sso.setOptions(ssoOptions);
            if (initialized) {
                return;
            }
            if (options.initTime && (new Date) / 1000 > options.initTime + TTL_ONE_DAY) {
                w.location.reload();
                return;
            }
            initialized = true;
            if ($p.options.isSsoServer) {
                msgTargetOrigin = d.referrer;
                _listen(w, "message", function (e) {
                    _serverOnMessage.apply(sso, [e]);
                });
            } else {
                msgTargetOrigin = options.ssoServer;
                _listen(w, "message", function (e) {
                    _clientOnMessage.apply(sso, [e]);
                });
            }
        },
        setOptions: function (ssoOptions) {
            if (ssoOptions) for (var key in ssoOptions) {
                if (ssoOptions.hasOwnProperty(key)) {
                    options[key] = ssoOptions[key];
                }
            }
            return sso;
        },
        check: function () {
            if (!initialized) {
                throw new Error("Please invoke $p.sso.init() first.");
            }
            _debug("Start checking");
            var clientUid = sso.getUid(),
                message = {debug: options.debug, clientUid: clientUid, check: true};
            if (iframe) {
                return serverWindow && _sendMsgTo(serverWindow, message);
            }
            iframe = d.createElement("iframe");
            iframe.src = options.ssoServer + options.ssoCheckUri;
            iframe.width = iframe.height = iframe.frameBorder = 0;
            iframe.style.display = "none";
            _listen(iframe, "load", function () {
                serverWindow = iframe.contentWindow;
                _sendMsgTo(serverWindow, message);
            });
            d.getElementsByTagName("body")[0].appendChild(iframe);
        },
        stopCheck: function () {
            _sendMsgTo(serverWindow, {stopCheck: true});
        },
        getUid: function () {
            return simpleStorage && simpleStorage.get("uid");
        },
        setUid: function (uid, ttl) {
            _debug("Set uid: " + uid);
            simpleStorage && simpleStorage.set("uid", uid, {TTL: ttl || 0});
        }
    };

    function _clientLogin(loginData) {
        _debug("Client login");
        _debug(loginData);
        // TODO Invoke client notify url to finish login
        sso.setUid(loginData.uid);
    }

    function _clientLogout() {
        vars.clientUid && _debug("Client logout");
        // TODO Invoke client notify url to finish logout
        sso.setUid(null);
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

    function _debug(info) {
        w.console && options.debug && w.console.log(info);
    }

    function _getJson(data) {
        var jsonData;
        try {
            jsonData = w.JSON.parse(data);
            return jsonData;
        } catch (E) {
            return data;
        }
    }

    function _jsonStringify(obj) {
        return w.JSON.stringify(obj);
    }

    function _listen(host, eventName, callback) {
        if ("addEventListener" in host) {
            host.addEventListener(eventName, callback, false);
        } else {
            host.attachEvent("on" + eventName, callback);
        }
    }

    function _sendMsgTo(frame, message) {
        frame.postMessage(typeof message == "string" ? message : _jsonStringify(message), msgTargetOrigin);
    }

    function _serverCheck() {
        var clientUid = vars.clientUid,
            serverUid = sso.getUid(),
            loginData;
        clientWindow = w.parent;
        timerServerCheck = setTimeout(function () {
            _serverCheck.apply(sso);
        }, 1000);
        if (clientUid == serverUid) {
            return;
        }
        _debug("Server uid: " + serverUid);
        if (serverUid) {
            // Login
            _debug("Login: " + serverUid);
            loginData = {uid: serverUid};
            // TODO Fill loginData via ajax request to sso server
            vars.clientUid = serverUid;
            _sendMsgTo(clientWindow, {login: loginData});
        } else {
            // Logout
            clientUid && _debug("Logout");
            vars.clientUid = null;
            _sendMsgTo(clientWindow, {logout: true});
        }
    }

    function _serverOnMessage(e) {
        var data = _getJson(e.data),
            clientUid;
        if (data.debug) {
            options.debug = true;
        }
        _debug("Handle in iframe");
        if (clientUid = data.clientUid) {
            _debug("Aware client uid: " + clientUid);
            vars.clientUid = clientUid;
        }
        if (data.check) {
            _serverCheck.apply(sso);
        }
        if (data.stopCheck) {
            _debug("Stop checking");
            clearTimeout(timerServerCheck);
        }
    }

    if (!w.JSON) {
        throw new Error("Please include JSON support script to your page.");
    }
    _listen(w, "load", function () {
        sso.init();
    });
}(window, document);
