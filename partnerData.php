<?php
    $partnerData = array
    (
        'agora' => array
        (
            'ids' => array
            (
                '2022562007' => array('channel' => '2022562007', 'app' => '99d971190d3a40408675446ac61548c3'),
                '2022562008' => array('channel' => '2022562008', 'app' => '99d971190d3a40408675446ac61548c3'), 
                '2022562009' => array('channel' => '2022562009', 'app' => '99d971190d3a40408675446ac61548c3')
            )
        ),
        'test' => array
        (
            'ids' => array
            (
                '2022562007' => array('channel' => '2022562007', 'app' => '99d971190d3a40408675446ac61548c3'),
                '2022562008' => array('channel' => '2022562008', 'app' => '99d971190d3a40408675446ac61548c3'), 
                '2022562009' => array('channel' => '2022562009', 'app' => '99d971190d3a40408675446ac61548c3')
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