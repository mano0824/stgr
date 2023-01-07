<?php
return [
    'Sgsp_Pay_Check_Res' => [
        'MachineNo' => '',
        'CallNo' => '',
        'ListDetail' => [
            (int) 0 => [
            'TrkNo' => 'TrkNo01',
            'TrkName' => 'Tachi',
            'TrkKana' => '',
            'Kingaku' => 1000,
            'Status' => '',
            'Message' => 'Gift a voucher　Tachi ',
            ],
            (int) 1 => [
             'TrkNo' => 'TrkNo02',
            'TrkName' => 'Jana',
            'TrkKana' => '',
            'Kingaku' => 2000,
            'Status' => '',
            'Message' => '',
            ],
            (int) 2 => [
             'TrkNo' => 'TrkNo03',
            'TrkName' => 'Ichi',
            'TrkKana' => '',
              'Kingaku' => 3000,
            'Status' => '',
            'Message' => 'Gift a voucher　Ichi',
            ],
            (int) 3 => [
             'TrkNo' => 'TrkNo04',
            'TrkName' => 'Neco',
            'TrkKana' => '',
              'Kingaku' => 4000,
            'Status' => '',
            'Message' => '',
            ]
        ]
    ],
    'Sgsp_Pay_Check_Detail_Res' => [
        'MachineNo' => '',
        'CallNo' => '',
        'TrkName' => 'Tachi',
        'TrkNo'=> 'TrkNo1',
        'Detail'=> [
            (int) 0 => [
                'DispSortNo'=> '7',
                'ShohinName'=> 'Soda',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '2',
                'Kingaku'=> 1000,
                'Status'=> '0'
            ],
            (int) 1 => [
                'DispSortNo'=> '1',
                'ShohinName'=> 'Oshi',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ],
            (int) 2 => [
                'DispSortNo'=> '3',
                'ShohinName'=> 'Wassabi',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ],
            (int) 3 => [
                'DispSortNo'=> '2',
                'ShohinName'=> 'Ramen',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ],
            (int) 4 => [
                'DispSortNo'=> '4',
                'ShohinName'=> 'Susshi',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ],
            (int) 5 => [
                'DispSortNo'=> '5',
                'ShohinName'=> 'Mocchi cake',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ],
            (int) 6 => [
                'DispSortNo'=> '6',
                'ShohinName'=> 'Tea',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ],
            (int) 7 => [
                'DispSortNo'=> '1',
                'ShohinName'=> 'Sake',
                'ZeiKbn'=> '0',
                'Tanka'=> '10',
                'Suryo'=> '1',
                'Kingaku'=> 5000,
                'Status'=> '0'
            ]
        ]
    ],
    'Sgsp_Pay_Res' => [
        'MachineNo' => '',
        'CallNo' => '',
        'HostBillNo' => '',
        'Status' => '',
    ],
    'Sgsp_Pay_Reset_Res' => [
        'MachineNo' => 1,
        'CallNo' => '',
        'Status' => 0,
    ],
    'Sgsp_Pay_Receipt_Res' => [
        'MachineNo' => 11,
        'CallNo' => '',
        'ReceiptKbn' => '',
        'ReceiptNo' => 789,
        'TrkNo' => '',
        'Comment1' => 'お客様感謝プレゼント1',
        'Comment2' => 'お客様感謝プレゼント2',
        'Comment3' => 'お客様感謝プレゼント3',
        'Comment4' => 'お客様感謝プレゼント4',
        'Comment5' => 'お客様感謝プレゼント5',
        'Comment6' => '',
        'Comment7' => '    お客様感謝7',
        'Comment8' => '    お客様感謝8',
        'Status' => '0',
        'ListTax' => [
            (int) 0 => [
                'RowNo' => 0,
                'DetailKbn' => 'E',
                'ShohinName' => '内ゴルフ場利用税データE',
                'Tanka' => 100,
                'Suryo' => '1',
                'Kingaku' => 100,
            ],
            (int) 1 => [
                'RowNo' => 2,
                'DetailKbn' => 'D',
                'ShohinName' => '内消費税データ（総合計2）D',
                'Tanka' => 5000,
                'Suryo' => '3',
                'Kingaku' => 1200,
            ],
            (int) 2 => [
                'RowNo' => 1,
                'DetailKbn' => 'C',
                'ShohinName' => '内税データ（拡張）C',
                'Tanka' => 200,
                'Suryo' => '3',
                'Kingaku' => 1300,
            ],
            (int) 3 => [
                'RowNo' => '3',
                'DetailKbn' => 'G',
                'ShohinName' => '内税データ（拡張）G',
                'Tanka' => '4000',
                'Suryo' => '3',
                'Kingaku' => 1500,
            ],
            (int) 4 => [
                'RowNo' => '4',
                'DetailKbn' => 'N',
                'ShohinName' => '商品名 N',
                'Tanka' => 4000,
                'Suryo' => '4',
                'Kingaku' => 40000,
            ],
            (int) 5 => [
                'RowNo' => '5',
                'DetailKbn' => 'N',
                'ShohinName' => '商品名5 N',
                'Tanka' => 5000,
                'Suryo' => '5',
                'Kingaku' => 50000,
            ],
            (int) 6 => [
                'RowNo' => '6',
                'DetailKbn' => 'N',
                'ShohinName' => '商品名6 N',
                'Tanka' => 6000,
                'Suryo' => '6',
                'Kingaku' => 60000,
            ],
            (int) 7 => [
                'RowNo' => '7',
                'DetailKbn' => 'N',
                'ShohinName' => '商品名7 N',
                'Tanka' => 7000,
                'Suryo' => '99',
                'Kingaku' => 70000,
            ],
            (int) 8 => [
                'RowNo' => '8',
                'DetailKbn' => 'T',
                'ShohinName' => 'トータル T',
                'Tanka' => 3000,
                'Suryo' => '3',
                'Kingaku' => 987654,
            ],
            (int) 9 => [
                'RowNo' => '9',
                'DetailKbn' => 'J',
                'ShohinName' => '*',
                'Tanka' => 3000,
                'Suryo' => '3',
                'Kingaku' => 1101,
            ],
            (int) 10 => [
                'RowNo' => '10',
                'DetailKbn' => 'K',
                'ShohinName' => '#',
                'Tanka' => 3000,
                'Suryo' => '3',
                'Kingaku' => 1101,
            ]
        ]
    ],
    'Sgsp_Pay_Purchase_Res' => [
        'MachineNo' => '',
        'CallNo' => '',
        'Status' => '',
    ],
    'Sgsp_Pay_Purchase_Decision_Res' => [
        'MachineNo' => '',
        'CallNo' => '',
        'Status' => '',
    ],
    'Sgsp_PointPay_Check_Res' => [
        'MachineNo' => '',
        'CallNo' => '',
        'ListDetail' => [
            (int) 0 => [
            'TrkNo' => 'TrkNo01',
            'TrkName' => 'Tachi',
            'TrkKana' => '',
            'Kingaku' => 1000,
            'Status' => '',
            'Message' => 'Gift a voucher Tachi',
            ],
            (int) 1 => [
            'TrkNo' => 'TrkNo02',
            'TrkName' => 'Jana',
            'TrkKana' => '',
            'Kingaku' => 2000,
            'Status' => '',
            'Message' => '',
            ],
            (int) 2 => [
             'TrkNo' => 'TrkNo03',
            'TrkName' => 'Ichi',
            'TrkKana' => '',
            'Kingaku' => 3000,
            'Status' => '',
            'Message' => 'Gift a voucher Ichi',
            ],
            (int) 3 => [
             'TrkNo' => 'TrkNo04',
            'TrkName' => 'Neco',
            'TrkKana' => '',
            'Kingaku' => 4000,
            'Status' => '',
            'Message' => '',
            ]
        ],
        'BillingAmount' => '',
        'UsePoints' => 1000,
        'UsePointsKind' => '',
        'PointBalance' => 9000,
        'UsePointsLimitUpper' => 10000,
        'UsePointsLimitLower' => 10,
        'UsePointsUnit' => '',
        'UsePointsMessage' => ''
    ],
];
