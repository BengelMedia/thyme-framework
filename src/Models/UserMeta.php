<?php

namespace Thyme\Framework\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $table = 'usermeta';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'meta_key',
        'meta_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}