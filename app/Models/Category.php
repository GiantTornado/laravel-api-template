<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    public function books() {
        // return $this->hasMany(RelatedModel::class, 'foreign_key_in_related_model', 'primary_key_in_current_model');
        return $this->hasMany(Book::class, 'category_id', 'id');
    }
}
