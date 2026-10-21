<?php

return [
    'materials' => [
        // 6 Grade Lama (Baseline Wajib Identik)
        'CF8'          => ['standard' => 'ASTM A351',     'alias' => '304', 'label' => 'CF8 (304)'],
        'CF8M'         => ['standard' => 'ASTM A351',     'alias' => '316', 'label' => 'CF8M (316)'],
        'SCS13A'       => ['standard' => 'JIS G 5121',    'alias' => '304', 'label' => 'SCS13A (304)'],
        'SCS14A'       => ['standard' => 'JIS G 5121',    'alias' => '316', 'label' => 'SCS14A (316)'],
        '1.4308'       => ['standard' => 'BS EN 10213',   'alias' => '304', 'label' => 'GX5CrNi19-10 (1.4308)'],
        '1.4408'       => ['standard' => 'BS EN 10213',   'alias' => '316', 'label' => 'GX5CrNiMo19-11-2 (1.4408)'],

        // 3 Grade Baru
        '316 (S31600)' => ['standard' => 'ASTM A959',     'alias' => '316', 'label' => '316 / S31600 (ASTM A959)'],
        'SUS316'       => ['standard' => 'JIS G4403',     'alias' => '316', 'label' => 'SUS316 (JIS G4403)'],
        '1.4401'       => ['standard' => 'BS EN 10088-1', 'alias' => '316', 'label' => '1.4401 (BS EN 10088-1)'],
    ],

    'params' => [
        // 6 Grade Lama (Baseline Wajib Identik)
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
                'hb'        => [null, 183],
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
                'hb'        => [null, 183],
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
                'hb'        => [null, 183],
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
                'hb'        => [null, 183],
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
                'hb'        => [null, 183],
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
                'hb'        => [null, 183],
            ],
        ],

        // 3 Grade Baru
        '316 (S31600)' => [
            'chem' => [
                'c'  => [null, 0.08],
                'si' => [null, 1.00],
                'mn' => [null, 2.00],
                'p'  => [null, 0.045],
                's'  => [null, 0.030],
                'cr' => [16.00, 18.00],
                'ni' => [10.00, 14.00],
                'mo' => [2.00, 3.00],
            ],
            'mech' => [ // Mengikuti CF8M
                'ys_mpa'    => [205, null],
                'uts_mpa'   => [485, null],
                'elong_pct' => [30, null],
                'hb'        => [null, 183],
            ],
        ],

        'SUS316' => [
            'chem' => [
                'c'  => [null, 0.08],
                'si' => [null, 1.00],
                'mn' => [null, 2.00],
                'p'  => [null, 0.045],
                's'  => [null, 0.030],
                'cr' => [16.00, 18.00],
                'ni' => [10.00, 14.00],
                'mo' => [2.00, 3.00],
            ],
            'mech' => [ // Mengikuti SCS14A
                'ys_mpa'    => [205, null],
                'uts_mpa'   => [480, null],
                'elong_pct' => [33, null],
                'hb'        => [null, 183],
            ],
        ],

        '1.4401' => [
            'chem' => [
                'c'  => [null, 0.07],
                'si' => [null, 1.00],
                'mn' => [null, 2.00],
                'p'  => [null, 0.045],
                's'  => [null, 0.015],
                'cr' => [16.50, 18.50],
                'ni' => [10.00, 13.00],
                'mo' => [2.00, 2.50],
            ],
            'mech' => [ // Mengikuti 1.4408
                'ys_mpa'    => [210, null],
                'uts_mpa'   => [440, 640],
                'elong_pct' => [30, null],
                'hb'        => [null, 183],
            ],
        ],
    ],

    // Daftar parameter kimia baku terurut untuk tampilan tabel (12 baris)
    'chem_display_order' => ['c', 'si', 'mn', 'p', 's', 'cr', 'ni', 'mo', 'cu', 'co', 'al', 'v'],

    // Metadata units & labels pendukung
    'units' => [
        'chem' => '% wt',
        'mech' => [
            'ys_mpa'    => 'MPa',
            'uts_mpa'   => 'MPa',
            'elong_pct' => '%',
            'hb'        => 'HB',
        ],
    ],

    'labels' => [
        'chem' => [
            'c'  => 'Carbon (C)',
            'si' => 'Silicon (Si)',
            'mn' => 'Manganese (Mn)',
            'p'  => 'Phosphorus (P)',
            's'  => 'Sulphur (S)',
            'cr' => 'Chromium (Cr)',
            'ni' => 'Nickel (Ni)',
            'mo' => 'Molybdenum (Mo)',
            'cu' => 'Copper (Cu)',
            'co' => 'Cobalt (Co)',
            'al' => 'Aluminium (Al)',
            'v'  => 'Vanadium (V)',
            'n'  => 'Nitrogen (N)',
        ],
        'mech' => [
            'ys_mpa'    => 'Yield Strength',
            'uts_mpa'   => 'Ultimate Tensile Strength',
            'elong_pct' => 'Elongation',
            'hb'        => 'Brinell Hardness',
        ],
    ],
];
