<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $id
 * @property $room_id
 * @property $from_user_id
 * @property $body
 * @property $created_at
 * @property $updated_at
 */
class Message extends Model {
    use HasFactory;

    public $timestamps = true;

    protected $with = ['user'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'from_user_id');
    }

}
