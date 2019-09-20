<?php
    $partnerData = array
    (
        'agora' => array
        (
            'ids' => array
            (
                '2022562007' => array('channel' => '2022562007', 'app' => '99d971190d3a40408675446ac61548c3'),
                '2022562008' => array('channel' => '2022562008', 'app' => '99d971190d3a40408675446ac61548c3'), 
                '2022562009' => array('channel' => '2022562009', 'app' => '99d971190d3a40408675446ac61548c3'),
                '2022562010' => array('channel' => '2022562010', 'app' => '4c457b9120454344a25d355703f3d57f', 'keyURL' => 'https://sa-demo.agora.io:8036/token/rtc?uid=0&channel=$$CHANNEL$$&token_expire_ts=0&priviledge_expire_ts=0&salt=1')
            )
        ),
        'test' => array
        (
            'ids' => array
            (
                '2022562007' => array('channel' => '2022562007', 'app' => '99d971190d3a40408675446ac61548c3'),
                '2022562008' => array('channel' => '2022562008', 'app' => '99d971190d3a40408675446ac61548c3'), 
                '2022562009' => array('channel' => '2022562009', 'app' => '99d971190d3a40408675446ac61548c3'),
                '2022562010' => array('channel' => '2022562010', 'app' => '4c457b9120454344a25d355703f3d57f', 'keyURL' => 'https://sa-demo.agora.io:8036/token/rtc?uid=0&channel=$$CHANNEL$$&token_expire_ts=0&priviledge_expire_ts=0&salt=1')
            )
        )
    );

    function validateID($partner, $id, &$error)
    {
        global $partnerData;
        if (!array_key_exists($partner, $partnerData))
        {
            $error = "Invalid partners";
            return false;
        }
        if (!array_key_exists($id, $partnerData[$partner]['ids']))
        {
            $error = "Invalid ID";
            return false;
        }
        
        $retVal = $partnerData[$partner]['ids'][$id];
        if (isset($retVal['keyURL']))
        {
            $url = str_replace('$$CHANNEL$$', $retVal['channel'], $retVal['keyURL']);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $returnStr = curl_exec($ch);
            if ($returnStr === false)
            {
                $error = "keyURL failed with curlError: " . curl_error($ch);
                return false;
            }
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($code / 100 != 2)
            {
                $error = "keyURL failed with code: " . $code;
                return false;
            }
            
            $retVal['channelKey'] = $returnStr;
            unset($retVal['keyURL']);
        }
        
        return $retVal;
    }
?>