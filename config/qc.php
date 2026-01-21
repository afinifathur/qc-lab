<?php

return [
    'materials' => [
        'CF8'    => ['standard' => 'ASTM A351',   'alias' => '304'],
        'CF8M'   => ['standard' => 'ASTM A351',   'alias' => '316'],
        'SCS13A' => ['standard' => 'JIS G 5121',  'alias' => '304'],
        'SCS14A' => ['standard' => 'JIS G 5121',  'alias' => '316'],
        '1.4308' => ['standard' => 'BS EN 10213', 'alias' => '304'],
        '1.4408' => ['standard' => 'BS EN 10213', 'alias' => '316'],
    ],

    'params' => [
        'CF8' => [
            'chem' => [
                'c'  => [null, 0.08],
                'si' => [null, 2.00],
                'mn' => [null, 1.50],
                'p'  => [null, 0.04],
                's'  => [null, 0.04],
                'cr' => [18.00, 21.00],
                'ni' => [8.00, 11.00],
                'mo' => [null, 0.50],
                'cu' => [null, null],
            ],
            'mech' => [
                'ys_mpa'    => [205, null],
                'uts_mpa'   => [485, null],
                'elong_pct' => [35, null],
                'hb'        => [null, 187],
            ],
        ],

        'CF8M' => [
            'chem' => [
                'c'  => [null, 0.08],
                'si' => [null, 1.50],
                'mn' => [null, 1.50],
                'p'  => [null, 0.04],
                's'  => [null, 0.04],
                'cr' => [18.00, 21.00],
                'ni' => [9.00, 11.00],
                'mo' => [2.00, 3.00],
                'cu' => [null, null],
            ],
            'mech' => [
                'ys_mpa'    => [205, null],
                'uts_mpa'   => [485, null],
                'elong_pct' => [30, null],
                'hb'        => [null, 187],
            ],
        ],

        'SCS13A' => [
            'chem' => [
                'c'  => [null, 0.08],
                'si' => [null, 2.00],
                'mn' => [null, 1.50],
                'p'  => [null, 0.04],
                's'  => [null, 0.04],
                'cr' => [18.00, 21.00],
                'ni' => [8.00, 11.00],
            ],
            'mech' => [
                'ys_mpa'    => [205, null],
                'uts_mpa'   => [480, null],
                'elong_pct' => [33, null],
                'hb'        => [null, 187],
            ],
        ],

        'SCS14A' => [
            'chem' => [
                'c'  => [null, 0.08],
                'si' => [null, 1.50],
                'mn' => [null, 1.50],
                'p'  => [null, 0.04],
                's'  => [null, 0.04],
                'cr' => [18.00, 21.00],
                'ni' => [9.00, 12.00],
                'mo' => [2.00, 3.00],
            ],
            'mech' => [
                'ys_mpa'    => [205, null],
                'uts_mpa'   => [480, null],
                'elong_pct' => [33, null],
                'hb'        => [null, 187],
            ],
        ],

        '1.4308' => [
            'chem' => [
                'c'  => [null, 0.07],
                'si' => [null, 1.50],
                'mn' => [null, 1.50],
                'p'  => [null, 0.04],
                's'  => [null, 0.03],
                'cr' => [18.00, 20.00],
                'ni' => [8.00, 11.00],
                'cu' => [null, 0.50],
            ],
            'mech' => [
                'ys_mpa'    => [200, null],
                'uts_mpa'   => [440, 640],
                'elong_pct' => [30, null],
                'hb'        => [null, 187],
            ],
        ],

        '1.4408' => [
            'chem' => [
                'c'  => [null, 0.07],
                'si' => [null, 1.50],
                'mn' => [null, 1.50],
                'p'  => [null, 0.04],
                's'  => [null, 0.03],
                'cr' => [18.00, 20.00],
                'ni' => [9.00, 12.00],
                'mo' => [2.00, 2.50],
                'cu' => [null, 0.50],
            ],
            'mech' => [
                'ys_mpa'    => [210, null],
                'uts_mpa'   => [440, 640],
                'elong_pct' => [30, null],
                'hb'        => [null, 187],
            ],
        ],
    ],
];
