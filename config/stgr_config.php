<?php
// 注意！Windows「メモ帳」で編集・保存しないでください。UTF-8のBOMがついてしまいPHPが実行できなくなります。
// BOMなしで保存できるエディタをご使用ください。
return [
    'STGR' => [
        'GOLF' => [
            'FixedResponse' => false,                // 固定応答モード
            'DEBUG' => true,                        // デバッグログ出力モード
            'TimeOut' => 180,                        // 接続タイムアウト秒数
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
        ],
        'API' => [
            'DEBUG' => true,                       // デバッグログ出力モード
            'AllowExternalAccess' => true,         // 接続許可
            'DashboardURL' => 'http://ADC-5300G:88/dashboard-G/',          //Dashboard接続先URL
        ],
        'WebAPI' => [ // WebAPI標準化テスト環境
            'FixedResponse' => false,                // 固定応答モード
            'DEBUG' => true,                        // デバッグログ出力モード
            'TimeOut' => 180,                        // 接続タイムアウト秒数
            'ServiceURL' => 'http://172.16.35.169:3001/stgr/webapi/',            // 上位通信アプリ接続先URL
            'urlParams' => [
                'PayCheck' => 'sgwebapipaycheck',
                'Pay' => 'sgwebapipay',
                'PayReset' => 'sgwebapipayreset',
                'PayReceipt' => 'sgwebapipayreceipt',
                'PayPurchase' => 'sgwebapipaypurchase',
                'PayPurchaseDecision' => 'sgwebapipaypurchasedecision',
                'PointPayCheck' => 'sgwebapipointpaycheck'
            ]
        ],
    ],
];
