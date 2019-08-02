<?php
    $partnerData = array
    (
        'agora' => array
        (
            'ids' => array
            (
                '2022562007' => array('channel' => '2022562007'),
                '2022562008' => array('channel' => '2022562008'), 
                '2022562009' => array('channel' => '2022562009')
            )
        )
    );

    function validateID($partner, $id)
    {
        global $partnerData;
        if (!array_key_exists($partner, $partnerData))
            return false;
        if (!array_key_exists($id, $partnerData[$partner]['ids']))
            return false;
        
        return $partnerData[$partner]['ids'][$id];
    }
?>