<?php

namespace App\Models;

use App\Casts\UploadedFilesCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class References extends Model
{
    use HasFactory;

    protected $table = 'references';

    protected $fillable = [
        'user_id',
        'topic',
        'message',
        'attachments',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'attachments' => UploadedFilesCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
