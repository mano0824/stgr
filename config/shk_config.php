<?php
// 注意！Windows「メモ帳」で編集・保存しないでください。UTF-8のBOMがついてしまいPHPが実行できなくなります。
// BOMなしで保存できるエディタをご使用ください。
return [
    'STGR' => [
        'GOLF' => [
            'FixedResponse' => true,                // 固定応答モード
            'Debug' => true,                        // デバッグログ出力モード
            'TimeOut' => 60,                        // 接続タイムアウト秒数
            'ServiceURL' => 'http://localhost:688/api/',            // 上位通信アプリ接続先URL
            'urlParams' => [
                'PayCheck' => 'sgsppaycheck',
                'PayCheckDetail' => 'sgsppaycheckdetail',
                'Pay' => 'sgsppay',
                'PayReset' => 'sgsppayreset',
                'PayReceipt' => 'sgsppayreceipt',
                'PayPurchase' => 'sgsppaypurchase',
                'PayPurchaseDecision' => 'sgsppaypurchasedecision',
                'PointPayCheck' => 'sgsppointpaycheck'
            ]
        ]
    ]
];
