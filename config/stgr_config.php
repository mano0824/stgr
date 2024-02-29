<?php
// 注意！Windows「メモ帳」で編集・保存しないでください。UTF-8のBOMがついてしまいPHPが実行できなくなります。
// BOMなしで保存できるエディタをご使用ください。
return [
    'STGR' => [
        'GOLF' => [
            'FixedResponse' => false,                // 固定応答モード
            'Debug' => true,                        // デバッグログ出力モード
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
            'Debug' => true,                       // デバッグログ出力モード
            'AllowExternalAccess' => true,         // 接続許可
            'DashboardURL' => 'http://ADC-5300G:88/dashboard-G/',          //Dashboard接続先URL
        ],
        'SanwaAPI' => [
            'FixedResponse' => false,                // 固定応答モード
            'Debug' => true,                        // デバッグログ出力モード
            'TimeOut' => 180,                        // 接続タイムアウト秒数
            // 'ServiceURL' => 'http://localhost:88/sgsanwaapi/',            // 上位通信アプリ接続先URL
            'ServiceURL' => 'http://172.16.35.169:3001/stgr/sgsanwaapi/',            // mockoonローカル用
            // 'ServiceURL' => 'http://127.0.0.1:3001/stgr/sgsanwaapi/',            // mockoonサーフェス用
            'X-Api-Key' => 'UzUb6zbJugJUiYP9',
            'X-Manufacturer-Key' => 'hU9UqF2uYPEry2BM',
            'urlParams' => [
                'PayCheck' => 'inquire',
                'Pay' => 'pay',
                'PayReset' => 'cancel',
                'PayReceipt' => 'receipt',
                'PointPayCheck' => 'inquire'
            ]
        ],
        'TestSanwaAPI' => [
            'FixedResponse' => false,                // 固定応答モード
            'Debug' => true,                        // デバッグログ出力モード
            'TimeOut' => 180,                        // 接続タイムアウト秒数
            'ServiceURL' => 'http://3.113.130.61:50001/api/v1/SystemGearTest/apm/payments/',            // 上位通信アプリ接続先URL
            'X-Api-Key' => 'UzUb6zbJugJUiYP9',
            'X-Manufacturer-Key' => 'hU9UqF2uYPEry2BM',
            'urlParams' => [
                'PayCheck' => 'inquire',
                'Pay' => 'pay',
                'PayReset' => 'cancel',
                'PayReceipt' => 'receipt',
                'PointPayCheck' => 'inquire'
            ]
        ],
    ],
];
