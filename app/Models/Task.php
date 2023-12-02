<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'task';
    protected $fillable = [
        'title',
        'description',
        'usertask_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usertask_id', 'id');
    }
}