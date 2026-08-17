<?php

namespace Thyme\Framework\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * The primary post model for WordPress
 *
 * @property string $post_title
 * @property string $post_content
 * @property string $post_status
 * @property string $post_type
 * @property int $post_author
 *
 * @property-read  User $author
 * @property-read Collection<int, PostMeta> $meta
 *
 *
 *
 *
 */
class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'post_title',
        'post_content',
        'post_status',
        'post_type',
        'post_author',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'post_author');
    }

    public function meta()
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    public function scopePublished($query)
    {
        return $query->where('post_status', 'publish');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('post_type', $type);
    }
}