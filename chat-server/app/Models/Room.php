<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property $id
 * @property $name
 * @property $description
 * @property $created_at
 * @property $updated_at
 */
class Room extends Model {
    use HasFactory;

    public function messages(): HasMany {
        return $this->hasMany(Message::class, 'room_id');
    }

}
