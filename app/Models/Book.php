<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model {
    use HasFactory, Sluggable, SoftDeletes;

    protected $table = 'books';

    protected $primaryKey = 'id';

    //automatically fill [created_at] & [updated_at]
    public $timestamps = true;

    protected $fillable = ['title', 'slug', 'description', 'published_at', 'price', 'category_id'];

    protected function casts(): array {
        return [
            'published_at' => 'datetime'
        ];
    }

    public function sluggable(): array {      // auto generate slug on model creating [composer require cviebrock/eloquent-sluggable]
        return [
            'slug' => [
                'source' => 'title',         // name of column to generate [slug] from
            ],
        ];
    }

    protected function price(): Attribute {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $value / 100,
            set: fn(mixed $value, array $attributes) => $value * 100,
        );
    }

    public function scopeNewlyCreated($query, $from_date) {
        $query->where('created_at', '>=', $from_date);
    }

    /**
     * Get the category that the book belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category() {
        // return $this->belongsTo(RelatedModel::class, 'foreign_key_in_current_model', 'primary_key_in_related_model')->chained_methods;
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Get the authors associated with the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors() {
        return $this->belongsToMany(Author::class)->withPivot('is_autographed')->orderBy('name', 'desc');
    }
}
