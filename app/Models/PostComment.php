<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostComment extends Model
{
    protected $fillable = [
        'post_id',
        'name',
        'email',
        'comment',
    ];
    
    protected function casts(): array
    {
        return [];
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
