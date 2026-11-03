<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table         = 'messages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'email', 'subject', 'body'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'name'    => 'required|min_length[3]|max_length[100]',
        'email'   => 'required|valid_email|max_length[120]',
        'subject' => 'required|min_length[5]|max_length[150]',
        'body'    => 'required|min_length[10]|max_length[2000]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama wajib diisi.',
            'min_length' => 'Nama minimal 3 karakter.',
            'max_length' => 'Nama maksimal 100 karakter.',
        ],
        'email' => [
            'required'    => 'Email wajib diisi.',
            'valid_email' => 'Format email tidak valid.',
            'max_length'  => 'Email maksimal 120 karakter.',
        ],
        'subject' => [
            'required'   => 'Subjek wajib diisi.',
            'min_length' => 'Subjek minimal 5 karakter.',
            'max_length' => 'Subjek maksimal 150 karakter.',
        ],
        'body' => [
            'required'   => 'Pesan wajib diisi.',
            'min_length' => 'Pesan minimal 10 karakter.',
            'max_length' => 'Pesan maksimal 2000 karakter.',
        ],
    ];
}
