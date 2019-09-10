<?php
    require("partnerData.php");
    
    header("Content-Type: application/json");

    function returnError($error, $note = '')
    {
        $retVal = array
        (
            'commands' => array
            (
                array('trace'  => array('text' => 'ValidateID error: ' . $error . ', ' . $note)),
                array('hangup' => array('status' => $error, 'reasonHeader' => 'validateID error'))
            )
        );
        echo json_encode($retVal);
        exit();
    }

    if (!isset($_REQUEST['id']) || !isset($_REQUEST['partner']))
        returnError(500, 'Invalid params');
    
    $id      = trim($_REQUEST['id']);
    $partner = trim($_REQUEST['partner']);
    
    $resolvedID = validateID($partner, $id);
    if ($resolvedID === false)
        returnError(500, 'Invalid ID');
    
    $retVal = array
    (
        'merge' => true,
        'commands' => array
        (
            array('joinAgora' => array('channel' => $resolvedID['channel'], 'app' => $resolvedID['app'])),
            array('setAppID'  => array('id' => $resolvedID['channel'])),
            array('submit'    => array('url' => 'inConf.json'))
        )
    );
    echo json_encode($retVal);
?>