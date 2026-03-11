<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model {

    use HasFactory;

    protected $with = ['user'];
    protected $appends = ['created_at_fmt'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedAtFmtAttribute() {
        return $this->created_at->diffForHumans();
    }

}
