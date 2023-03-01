<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Iotronlab\FilamentMultiGuard\Tests\database\factories\PostFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
    ];

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
