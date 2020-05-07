Voice API
=========

Voice API is a language that controls user interactions with an IVR system on a
telephone call. It consists of Voice API documents that define a set of audio
operations, commands, event handlers, and variables.

Audio interactions with IVR are combined into dialogs. Dialogs specify audio
prompts to play, input to collect from the user, and event handlers active while
dialog is running.

Commands allow to start/stop/modify audio operations, provide ability to
interact with external web servers, and generate events.

Event handlers define actions to be performed when an event occurs. Events occur
when call has disconnected, or error occurred, or audio interaction state has
changed, etc.

Variables allow to store data for internal use in the session.

Starting Voice API
------------------

Voice API is instantiated either by an incoming call to a DID that is configured
for Voice API or by an DID portal API request that originates an outbound call.
In both cases, a URL to the initial Voice API document must be provided. Voice
API loads and executes initial document and follows the further instructions in
the document.

Initial Voice API settings:

-   url (required) -- initial document URL. Unless otherwise configured, the
    following fields are added to the request for initial document:

    -   sessionID -- session ID

    -   sessionUrl -- HTTP url for the call session

    -   accessMethod -- 0 for PSTN, 1 for SIP, 2 for WebCall

    -   toNumber -- number dialed

    -   fromNumber -- originating number

    -   callID -- SIP Call-ID

-   fetchAudioUrl -- audio to play while fetching initial document

-   fetchAudioDelay -- delay before starting fetch audio

-   fetchAudioMinDuration -- minimum duration of fetch audio to play

-   baseUrl -- base URL for HTTP URLs

-   audioBaseUrl -- base URL for Audio URLs

-   errorUrl -- url to submit errors to

-   callEventUrl -- url to submit call events to

-   maxDuration -- maximum duration of the call

-   vars -- a set of session variables. Partner, sessionID, and sessionUrl will
    be added to this set.

Voice API Document Structure
----------------------------

Voice API Document contains the following elements:

-   interdigitTimeout (int, optional) -- sets default interdigit timeout for all
    dialogs

-   merge (bool, optional) -- indicates whether this document will be merged with
    or overwrite previous document. When merging, dialogs and event handlers
    from previous document are preserved and new ones are merged into them (if
    same dialog or event exists in new document, it will overwrite existing).
    When overwriting, all dialogs and event handlers from previous document are
    removed and new ones added.

-   vars (array of *variable*, optional) -- variables (can be set in documents
    and with commands). Always merged.

-   events (array of *event*, optional) -- defines global document event handlers
    that will process events that are not processed by active dialog.

-   dialogs (array of *dialog*, optional) -- defines dialogs

-   commands (list of *command*, optional) -- commands that will be executed when
    document loads

Dialog elements:

-   name (string, required) -- name of the dialog

-   repeatCount (int, optional, default 0) -- number of times to the dialog can
    be repeated before generating "maxRepeat" event

-   timeout (int, optional) -- time to wait for user input after prompts have
    completed. When expires a "noInput" event is generated.

-   interdigitTimeout (int, optional) -- timeout between DTMF digits that
    completes input

-   inputs (array of *input*, optional) -- defines user inputs that will be
    collected in the dialog.

-   promptSets (array of *promptSet*, optional) -- defines prompt sets that will
    be played in the dialog. Dialogs can have several named prompt sets - a
    specific prompt set name can be specified when dialog is loaded.

-   events (array of *event*, optional) -- defines event handlers that are active
    while dialog is active.

PromptSet elements:

-   name (string, required) -- prompt set name

-   noBargeIn -- prompts not interrupted by input. Elements:

    -   prompts (list of *prompt*) -- list of prompts

-   bargeIn -- prompts that will interrupted by input. Elements:

    -   prompts (list of *prompt*) -- list of prompts

    -   stopPromptsOnInput (bool, optional, default: true) -- stop prompts when
        input is detected

Prompt elements:

-   url -- describes URL prompt. Elements:

    -   url (string, required) -- prompt URL

    -   maxAge (int, optional) -- max age

    -   maxStale (int, optional) -- max stale

-   input -- describes prompt constructed from the collected input. Elements:

    -   playAs (enum: digits, content, silence, dtmf; required) -- describes how
        to interpret input value

    -   dialog (string, required) -- name of dialog

    -   input (string, required) -- name of input

-   silence (int) -- silence duration

-   dtmf (string) -- plays DTMF tones

-   var -- describes prompt constructed from the stored variable. Elements:

    -   playAs (enum: digits, content, url, silence, dtmf; required) -- describes
        how to interpret variable value

    -   var (string, required) -- name of variable

Input elements:

-   DtmfOptions -- defines DTMF options grammar -- matches one of the pre-defined
    digit strings. Elements:

    -   tag (string, optional) -- grammar tag

    -   options (array or *option*)-- list of options. Elements:

        -   digits (string, required) -- digits to match

        -   commands (list of *command*, optional) -- commands that will execute
            when matched

-   DtmfCollect -- define DTMF collect grammar -- collects digits of specified
    length and/or termination. Elements:

    -   tag (string, optional) -- grammar tag

    -   minDigits (int, optional) -- minimum input length

    -   maxDigits (int, optional) -- maximum input length

    -   allowedDigits (string, optional) -- allowed digits

    -   termDigits(string, optional) -- terminating digits

    -   includeTermDigit(bool, optional) -- include terminating digit in
        collected input

    -   commands (list of *command*, optional) -- commands that will execute when
        matched.

-   VoiceRecord -- record audio of specified duration. Elements:

    -   tag (string, optional) -- grammar tag

    -   maxDuration (int, required) -- specifies maximum duration of audio to
        record

    -   maxSilence (int, optional) -- specifies maximum trailing silence (after
        non-silence) that will terminate the recording

    -   silenceThreshold (int, optional) -- minimum signal level that is
        considered non-silence

    -   termDigits (string, optional) -- terminating digits

    -   trimSilence (bool, optional) -- trip silence in the recording

    -   toneClamp (bool, optional) -- clamp DTMF tones in the audio recording

    -   commands (list of *command*, optional) -- commands that will execute when
        matched

Command elements:

-   trace -- write a trace to log. Elements:

    -   text (string, required) -- trace text

    -   level (int, optional) -- trace level

    -   data (array of *dataField*, optional) -- data fields to include in the
        trace

-   alarm -- write an alarm to log. Elements:

    -   text (string, required) -- alarm text

    -   data (array of *dataField*, optional) -- data fields to include in the
        alarm

-   setAppID -- set CDR appRefID to the specified value. Elements:

    -   id (string, required)

-   sendProvisional -- send provisional response to incoming call. Elements:

    -   status (int, optional, default 180) -- response status

    -   reason (string, optional) -- response reason

    -   reasonHeader (string, optional) -- response reason header

-   goTo -- activate a dialog. Elements:

    -   dialog (string, required) -- name of the dialog to activate

    -   promptSet (string, optional) -- name of promptSet to play

    -   inputTags (array of inputTag, optional)-- list of tags for the inputs to
        activate. Elements:

        -   tag (string, required) -- tag name

    -   onLoadEvent (string, optional) -- name of event to generate when dialog
        is loaded

-   send -- send a request. Elements:

    -   eventName (string, required) -- event name to generate when completes

    -   url (string, required) -- url to send request to

    -   type (string, optional) -- content type

    -   method (string, optional) -- method

    -   body (string, optional) -- literal body

    -   data (array of *dataField*, optional) -- data fields to include in
        request (encoded according to type)

    -   fetchTimeout (int, optional) -- request timeout

-   submit -- send a request and retrieve next document. Elements:

    -   url (string, required) -- url to send request to

    -   type (string, optional) -- content type

    -   method (string, optional) -- method

    -   body (string, optional) -- literal body

    -   data (array of *dataField*, optional) -- data fields to include in
        request (encoded according to type)

    -   fetchTimeout (int, optional) -- request timeout

    -   fetchAudioUrl -- audio to play while request in progress

    -   fetchAudioDelay -- delay before starting fetch audio

    -   fetchAudioMinDuration -- minimum duration of fetch audio to play

-   exit -- exit current dialog

-   stop -- stop document execution

-   hangup -- disconnect call. Elements:

    -   status (int, optional) -- disconnect status

    -   reason (string, optional) -- disconnect reason

    -   reasonHeader (string, optional) -- disconnect reason header

-   fastForward -- fast forward currently playing audio. Elements:

    -   msec (int, optional, default 1000 msec) -- duration to fast forward by

-   rewind -- rewind currently playing audio. Elements:

    -   msec (int, optional, default 1000 msec) -- duration to rewind by

-   pause -- pause currently playing audio. Elements:

    -   msec (int, optional) -- duration to pause for

-   pauseToggle -- toggle pause

-   resume -- resume audio

-   clearInput -- clear values collected by dialog inputs. Elements:

    -   dialog (string, optional) -- dialog to clear (default current dialog)

-   clearRepeat -- clear repeat counter of a dialog. Elements:

    -   dialog (string, optional) -- dialog to clear (default current dialog)

-   sendDTMF -- send DTMF digits on a call. Elements:

    -   digits (string, required) -- digits to send

    -   toneTime (int, optional, default 200 msec) -- tone duration

    -   breakTime (int, optional, default 100 msec) -- break duration

    -   pauseTime (int, optional, default 100 msec) -- pause duration

-   throw -- generate a named event. Elements:

    -   name (string, required) -- event name

    -   data (array of *dataField*, optional) -- data fields to include in event

-   joinAgora -- join Agora channel. Elements:

    -   channel (string, required) -- Agora channel name

    -   channelKey (string, optional) -- Agora channel key

    -   app (string, required) -- Agora app name

    -   uid (int, optional) -- Agora app uid

    -   broadcast (bool, optional) -- Agora app broadcast

    -   idleLimitSec (int, optional) -- Agora app idle limit

    -   playFromConf (int, optional, default 1) -- play audio from the conference
        to the call

    -   playToConf (int, optional, default 1) -- play audio from the call into
        the conference

    -   inGain (int, optional) -- incoming audio gain

    -   outGain (int, optional) -- outgoing audio gain

-   leaveAgora -- leave Agora channel.

-   playFromConf -- play audio from the conference to the call. Elements:

    -   value (int, optional) -- value to set

-   playToConf -- play audio from the call into the conference. Elements:

    -   value (int, optional) -- value to set

-   startTimer -- start timer. Elements:

    -   name (string, required) -- timer name (event with this name will be
        generated when timer expires)

    -   duration (int, required) -- timer duration

-   stopTimer -- stop timer. Elements:

    -   name (string, required) -- timer name

-   startHTTPListen -- start HTTP listener in the session. Elements

    -   path (string, required) -- http path

-   stopHTTPListen -- stop HTTP listener in the session. Elements

    -   path (string, required) -- http path

-   storeVar -- store variables

    -   data (array of *dataField*, optional) -- data fields to store

-   authorize -- authorize incoming call. Elements:

    -   user (string, required) -- authorization user

    -   password (string, required) -- authorization password

    -   realm (string, required) -- authorization realm

-   restartInput -- restart current input. Only available in commands executed on
    input match.

-   stopPrompts -- stop current prompts

-   respond -- sends a response. Only available in commands executed from the
    event injected by an API request. Elements:

    -   status (int, optional) -- response status

    -   type (string, optional) -- response content type

    -   body (string, optional) -- literal response body

    -   data (array of *dataField*, optional) -- data fields to include in
        response (encoded according to type)

DataField elements:

-   name (string, required) -- defines field name

-   value (required) -- source of the field value. Value can be:

    -   Input collected in a dialog. Elements:

        -   dialog (string, optional) -- dialog name

        -   input (string, required) -- name of input. The following input names
            are available:

            -   lastVoiceRecordData -- audio recording produced by last
                voiceRecord input

            -   lastVoiceRecordDuration -- duration of audio recording produced
                by last voiceRecord input

            -   lastVoiceRecordFormat -- format of audio recording produced by
                last voiceRecord input

            -   lastDtmfDigits -- digits detected by last dtmfOptions or
                dtmfCollect input

            -   lastDtmfTermDigit-- termination digit detected by last
                dtmfOptions or dtmfCollect input

            -   lastMatchReason-- match reason produced by last matched input

            -   repeatCount -- repeat counter of the dialog

    -   Field from the event. Elements:

        -   event (string, required) -- name of the event field

    -   Stored variable. Elements:

        -   var (string, required) -- variable name

    -   Call information field. Elements:

        -   callInfo (string, required) -- name of the call info field

    -   Call header field. Elements:

        -   callHeader (string, required) -- name of the call header

    -   Literal value. Elements:

        -   literal (string, required) -- literal value

Fixed Events -- events with predefined names generated in Voice API documents:

-   disconnected -- generated when call disconnects. Event contains fields:

    -   status -- disconnect status

    -   reason -- disconnect reason

    -   reasonHeader -- disconnect reason header

    -   accessMethod

    -   direction -- 0 for inbound, 1 for outbound

    -   requestUri -- request URI

    -   fromNumber -- from number

    -   fromName -- from name

    -   toNumber -- to number

    -   callID -- SIP call ID

    -   connectedStart -- timestamp when call was answered

    -   connectedStop -- timestamp when call was disconnected

    -   offHookStart -- timestamp when call went offhook

    -   offHookStop -- timestamp when call went on hook

    -   codec -- codec used in the call

    -   jitterAverage -- average RTP jitter

    -   jitterMaximum -- max RTP jitter

    -   droppedPackets -- number of RTP packets dropped

    -   latePackets -- number of RTP packets late

    -   receivedPackets -- number of packets received

    -   sentPackets -- number of packets sent

    -   remoteIP -- remote RTP address

    -   remotePort -- remote RTP port

    -   localIP -- local RTP address

    -   localPort -- local RTP port

-   callConnected -- generate when call is answered

-   badAudio -- generated when audio fetch failed

-   error -- generate when a document error occurs. Event contains fields:

    -   error -- error description

-   promptsDone -- generated when prompts completed playing

-   onLoad - generated when dialog loads (unless an alternative event name is
    provided in goTo command)

-   noInput -- generated when no input was received for a specified duration
    after prompts complete

-   noMatch -- generated when last active input failed to match. Event contains
    fields:

    -   digits -- digits currently received

    -   reason -- reason string

-   maxRepeat -- generated when repeatCount was exceeded in a dialog. Event
    contains fields:

    -   repeatCount -- current value of repeat count

-   httpListenStarted -- generated when startHTTPListen command succeeded. Event
    contains fields:

    -   url -- full URL for HTTP listener

    -   path -- path for HTTP listener

-   authorizeSuccess -- generated when authorize command succeeded

-   authorizeFailed -- generated when authorize command succeeded. Event contains
    fields:

    -   status -- stale, failed

-   sendFailed -- generated when send command failed. Event contains fields:

    -   name -- event name used in send command

    -   url -- url used in send command

Custom Events -- events with custom names that are generated by Voice API
commands:

-   when send command succeeds an event with name specified in command is
    generated. It contains fields that are provided in HTTP response.

-   when a timer initiated with startTimer command expires an event with name
    provided in the command is generated.

-   throw command generates an event with name provided in the command. The
    event contains fields provided in the command.

Voice API Document Loading and Execution
----------------------------------------

When next document is loaded, depending on the value of "merge" field, it will
be either merged with current document or will overwrite it. When merging,
dialogs and event handlers from current document are preserved and new ones are
merged into them (if the same dialog or event exists in new document, it will
overwrite current). When overwriting, all dialogs and event handlers from
current document are removed and new ones added. Variables are always merged.

Event handlers specified in the root of the document (global) are activated when
document is loaded.

Commands specified in the root of the document are executed when document is
loaded.

Default error handler (consisting of an alarm and hangup commands) is added when
fetching initial document and if current document does not have a handler for
"error" event.

Dialogs
-------

### Activation

Dialog is activated with a goTo command. If you wish to activate a dialog when
document is loaded a goTo command should be provided in the document "commands"
list. When a dialog is activated, any running fetchAudio prompts are stopped.

Optional "onLoadEvent" goTo command parameter specifies the name of the event
that will be generated when dialog is activated. If onLoadEvent is not
specified, a default event with name "onLoad" will be generated.

The same dialog can be activated multiple times. If "repeatCount" parameter is
specified and the number of times dialog has been activated exceeds this value,
a "maxRepeat" event is generated and the dialog does not proceed.

The dialog remains active until end, stop, hangup, or goTo command is executed.

When dialog is deactivated, all running prompts and input are terminated and
dialog-level event handlers are no longer active.

### Dialog Event Handlers

Events specified in the dialog are activated when dialog is activated. When an
event occurs it will be first match by event handlers in the dialog and if
nothing matched, then document-level event handlers are matched.

### Dialog Prompts

Optional promptSet parameter in goTo command specifies the prompt set that will
be activated. If promptSet is not specified, the first prompt set in the dialog
will be activated. First, prompts listed in "noBargeIn" are played then
"bargeIn" prompts.

When all prompts complete (or there were no prompts) "promptsDone" event is
generated.

If any user input is detected prompts are terminated unless "stopPromptsOnInput"
is set to false -- in which case prompts continue until "stopPrompts" command is
executed. Using "stopPromptsOnInput" set to false in combination with
"stopPrompts" and "restartInput" commands allows to implement actions without
interrupting the flow of prompts. For example, to allow user to fast forward,
rewind, and exit while listening to a long audio.

### Dialog Inputs

Optional inputTags parameter in goTo command specifies a list of input tags that
will be activated - all inputs that match any of the inputTags will be
activated. If inputTags is not specified, all input are activated.

If "noBargeIn" prompts are empty, inputs are activated when dialog is activated,
otherwise inputs are activated when "noBargeIn" prompts complete.

Multiple inputs can be active at the same time. Each input is matched
independently and continues to run until matched, no match is possible, or
dialog is deactivated. When the last input terminates and no match has occurred
then a "noMatch" event is generated.

When an input has matched, commands specified in this input are executed. Other
active inputs will continue to run while matched is still possible.

Input values that caused the match are available to the commands via event
fields.

The following event fields are available when dtmfOptions or dtmfCollect input
matched:

-   digits -- string that matched

-   termDigit -- terminating digit

-   reason -- option, maxDigits, termDigit, interdigitTimeout

The following event fields are available when voiceRecord input matched:

-   reason -- complete, termDigit

-   termDigit -- terminating digit

-   voiceRecordData - recording

-   voiceRecordDuration -- recording duration

-   voiceRecordFormat -- redording format

### No Input timeout

If timeout was specified on the dialog then noInput timer will be started when
all prompts complete (or there were no prompts). If user input is detected,
noInput timer is stopped. If noInput timer fires before any input is detected, a
"noInput" event is generated.

### Interdigit timer

If interdigitTimeout was specified on the document or dialog, then a interdigit
timer will be restarted after every digit is detected. If interdigit timeout
occurs, then running dtmfCollect inputs are evaluated for potential match -- if
match occurred then input's commands are executed, otherwise if there are no
more running inputs a noMatch event is generated.

### Dialogs activated after call is disconnected

If a dialog is activated after call is disconnected, neither prompts nor inputs
will be activated and "promptsDone" event is generated immediately upon
activation. This allows to finish sending data from the session.

There is a system limit of 20 dialog activations that may occur after call is
disconnected. After that no more dialogs will be activated.

Data Fields
-----------

Data Fields mechanism provides a way of storing data in session variables or
sending any collected or stored data to a URL.

If a field referenced in a data fields array does not exist an empty value is
substituted.
