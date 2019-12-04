<?php
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

    if (!isset($_REQUEST['id']) || !isset($_REQUEST['partner']) || !isset($_REQUEST['accessMethod']))
        returnError(500, 'Invalid params');
    
    $id             = trim($_REQUEST['id']);
    $partner        = trim($_REQUEST['partner']);
    $accessMethod   = $_REQUEST['accessMethod'];
    
    if ($accessMethod == 0)
    {
        if (!isset($_REQUEST['toNumber']))
            returnError(500, 'Invalid params');
        $toNumber = trim($_REQUEST['toNumber']);
        
        if ($toNumber[0] != '+')
            returnError(500, 'Invalid Number');

        $phoneNumberInputRules = array(
            '/^\+1(\d{8,})$/' => '+1$1'
        ); 
        
        $matched = false;
        foreach ($phoneNumberInputRules as $pattern => $replacement) 
        {
            $toNumber = preg_replace($pattern, $replacement, $toNumber, -1, $count);
            if ($count) 
            {
                $matched = true;
                break;
            }
        }
        if (!$matched)
            returnError(500, 'Number not allowed');
    }

    echo json_encode(array('validated' => true, 'maxDuration' => 180000));
?>