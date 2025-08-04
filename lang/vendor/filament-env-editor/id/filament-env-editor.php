<?php

return [
    'navigation' => [
        'group' => 'Sistem',
        'label' => 'Editor .Env',
    ],

    'page' => [
        'title' => 'Editor .Env',
    ],
    'tabs' => [
        'current-env' => [
            'title' => '.env Saat Ini',
        ],
        'backups' => [
            'title' => 'Cadangan',
        ],
    ],
    'actions' => [
        'add' => [
            'title' => 'Tambah Entri Baru',
            'modalHeading' => 'Tambah Entri Baru',
            'success' => [
                'title' => 'Kunci ":Name" berhasil ditulis',
            ],
            'form' => [
                'fields' => [
                    'key' => 'Kunci',
                    'value' => 'Nilai',
                    'index' => 'Sisipkan Setelah kunci yang ada (opsional)',
                ],
                'helpText' => [
                    'index' => 'Jika Anda perlu menempatkan entri baru ini setelah yang sudah ada, Anda dapat memilih salah satu kunci yang ada',
                ],
            ],
        ],
        'edit' => [
            'tooltip' => 'Edit Entri ":name"',
            'modal' => [
                'text' => 'Edit Entri',
            ],
        ],
        'delete' => [
            'tooltip' => 'Hapus entri ":name"',
            'confirm' => [
                'title' => 'Anda akan menghapus ":name" secara permanen. Apakah Anda yakin ingin menghapusnya?',
            ],
        ],
        'clear-cache' => [
            'title' => 'Bersihkan cache',
            'tooltip' => 'Terkadang Laravel menyimpan variabel ENV dalam cache, jadi Anda perlu membersihkan semua cache ("artisan optimize:clear") untuk membaca ulang perubahan .env',
        ],

        'backup' => [
            'title' => 'Buat Cadangan Baru',
            'success' => [
                'title' => 'Cadangan berhasil dibuat',
            ],
        ],
        'download' => [
            'title' => 'Unduh .env saat ini',
            'tooltip' => 'Unduh file cadangan ":name"',
        ],
        'upload-backup' => [
            'title' => 'Unggah file cadangan',
        ],
        'show-content' => [
            'modalHeading' => 'Konten mentah dari cadangan ":name"',
            'tooltip' => 'Tampilkan konten mentah',
        ],
        'restore-backup' => [
            'confirm' => [
                'title' => 'Anda akan memulihkan ":name" menggantikan file ".env" saat ini. Silakan konfirmasi pilihan Anda',
            ],
            'modalSubmit' => 'Pulihkan',
            'tooltip' => 'Pulihkan ":name" sebagai ENV saat ini',
        ],
        'delete-backup' => [
            'tooltip' => 'Hapus file cadangan ":name"',
            'confirm' => [
                'title' => 'Anda akan menghapus file cadangan ":name" secara permanen. Apakah Anda yakin ingin menghapusnya?',
            ],
        ],
    ],
];
