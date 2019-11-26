(function() {
  var API_URL = 'https://dids.turbobridge.com/api/1.0/';

  var authToken;
  var tbody;

  var SESSION_FIELDS = [
    'state', 'status', 'call', 'startTS', 'endTS', 'connectTS',
    'direction', 'accessMethod', 'requestUri', 'fromNumber',
    'fromName', 'fromUri', 'toNumber', 'toUri', 'pstnProvider',
    'tollFree', 'callID', 'url'
  ];

  function requestError(msg) {
    var str = 'request error';
    if (msg)
      str += ': ' + msg;

    alert(str);
  }

  function extractResult(response) {
    return response &&
      response.responseList &&
      response.responseList.requestItem &&
      response.responseList.requestItem[0] &&
      response.responseList.requestItem[0].result;
  }

  function sendRequest(auth, requestGroup, requestType, params, success) {
    var xhr = new XMLHttpRequest();

    xhr.onload = function() {
      if (xhr.status !== 200) {
        requestError();
        return;
      }

      console.log('got response: ' + xhr.response);

      var response;
      try {
        response = JSON.parse(xhr.response);
      } catch(e) {
        requestError('JSON.parse() error');
        return;
      }

      if (response.error) {
        requestError(response.error.code + ' ' + response.error.message);
        return;
      }

      if (!response.authToken) {
        requestError('no authToken');
        return;
      }

      success(response);
    };

    xhr.onerror = function() {
      requestError();
    };

    var requestBody = {
      request: Object.assign({}, auth),
    };
    if (requestType) {
      var subRequest = {};

      subRequest[requestType] = params || {};

      requestBody.request.requestList = [ subRequest ];
    }
    console.log('sending request: ' + JSON.stringify(requestBody, null, 2));

    xhr.open('POST', API_URL + requestGroup, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
  }

  function getAuthToken(e) {
    e.preventDefault();

    var auth = {
      authAdministrator: {
        username: this.username.value,
        password: this.password.value,
      },
    };

    sendRequest(auth, 'Authorize', null, null, getAuthTokenSuccess);
  }

  function getAuthTokenSuccess(response) {
    authToken = response.authToken;

    document.forms.loginForm.style.display = 'none';
    document.querySelector('#logged-in').style.display = 'block';
  }

  function getSessions() {
    var auth = {
      authToken: {
        token: authToken,
      },
    };

    var params = {
      appID: document.querySelector('#appID').value,
    };

    sendRequest(auth, 'VoiceAPI', 'getSessions', params, function(response) {
      var result = extractResult(response);
      if (!result) {
        requestError('missing result');
        return;
      }
      if (result.error) {
        requestError(result.error.code + ' ' + result.error.message);
        return;
      }
      updateSessions(result.session || []);
    });
  }

  function stopSession(sessionID) {
    var auth = {
      authToken: {
        token: authToken,
      },
    };

    var params = {
      sessionID: sessionID,
    };

    sendRequest(auth, 'VoiceAPI', 'stopSession', params, function(response) {
      var result = extractResult(response);
      if (!result) {
        requestError('missing result');
        return;
      }
      if (result.error) {
        requestError(result.error.code + ' ' + result.error.message);
        return;
      }

      alert('stopSession success');
    });
  }

  function mute(sessionID) {
    var auth = {
      authToken: {
        token: authToken,
      },
    };

    var params = {
      sessionID: sessionID,
      eventName: 'setMute',
      eventData: {},
    };

    sendRequest(auth, 'VoiceAPI', 'sendEvent', params, function(response) {
      var result = extractResult(response);
      if (!result) {
        requestError('missing result');
        return;
      }
      if (result.error) {
        requestError(result.error.code + ' ' + result.error.message);
        return;
      }

      alert('sendEvent success');
    });
  }

  function createButton(action) {
    var el = document.createElement('button');
    el.type = 'button';
    el.innerText = action;
    el.dataset.action = action;
    return el;
  }

  function updateSessions(sessions) {
    tbody.innerHTML = '';

    if (sessions.length) {
      sessions.forEach(addSession);
    } else {
      tbody.innerHTML = '<tr><td colspan="100">No sessions</td></tr>';
    }
  }

  function addSession(session) {
    var sessionID = session.sessionID;
    var tr = tbody.insertRow();

    tr.dataset.sessionID = sessionID;

    tr.insertCell().appendChild(createButton('stop'));
    tr.insertCell().appendChild(createButton('mute'));

    tr.insertCell().appendChild(document.createTextNode(session.appID));
    tr.insertCell().appendChild(document.createTextNode(sessionID));
    SESSION_FIELDS.forEach(function(field) {
      tr.insertCell().appendChild(document.createTextNode(session[field]));
    });
    tr.insertCell().appendChild(document.createTextNode(JSON.stringify(session['globals']), null, 2));
  }

  function initSessions() {
    tbody = document.querySelector('table tbody');

    updateSessions([]);
    document.forms.loginForm.onsubmit = getAuthToken;
    document.querySelector('#getSessions').onclick = getSessions;

    tbody.addEventListener('click', function(e) {
      if (e.target.tagName !== 'BUTTON')
        return;

      var tr = e.target.closest('tr');
      var sessionID = tr.dataset.sessionID;

      switch (e.target.dataset.action) {
      case 'stop':
        stopSession(sessionID);
        break;

      case 'mute':
        mute(sessionID);
        break;
      }
    });
  }

  window.addEventListener('load', initSessions);
})();
