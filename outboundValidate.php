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

    if (!isset($_REQUEST['id']) || !isset($_REQUEST['partner']) || !isset($_REQUEST['toNumber']))
        returnError(500, 'Invalid params');
    
    $id         = trim($_REQUEST['id']);
    $partner    = trim($_REQUEST['partner']);
    $toNumber   = trim($_REQUEST['toNumber']);
    
    $allowedIDs = array('2022562007' => 1, '2022562008' => 1, '2022562009' => 1);

    if (!array_key_exists($id, $allowedIDs) or $partner != 'agora')
        returnError(500, 'Invalid ID');

    if ($toNumber[0] != '+')
        returnError(500, 'Invalid Number');

    $phoneNumberInputRules = array(
        '/^\+1202(\d{5,})$/' => '+1202$1',
        '/^\+1240(\d{5,})$/' => '+1240$1',
        '/^\+1301(\d{5,})$/' => '+1301$1',
        '/^\+1951(\d{5,})$/' => '+1951$1'
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

    echo json_encode(array('validated' => true));
?>