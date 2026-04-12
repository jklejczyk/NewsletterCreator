<?php

namespace App\Domain\Article\Models;

use App\Domain\Article\Enums\ArticleCategory;
use App\Domain\Article\Enums\ArticleSource;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = ['title', 'content', 'summary', 'url', 'source', 'category',  'published_at', 'imported_at', 'is_processed'];

    protected $casts = [
        'category' => ArticleCategory::class,
        'source' => ArticleSource::class,
        'published_at' => 'datetime',
        'imported_at' => 'datetime',
    ];
}
