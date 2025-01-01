<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'description', 'published_at', 'category_id'];

    public function scopeNewlyCreated($query, $from_date)
    {
        $query->where("created_at", '>=', $from_date);
    }

    public function category()
    {
        // return $this->belongsTo(RelatedModel::class, 'foreign_key_in_current_model', 'primary_key_in_related_model')->chained_methods;
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class)->withPivot('is_autographed')->orderBy("name", "desc");
    }
}
