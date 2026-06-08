<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Overwrite Laravel's default 'id' assumption.
     * If you don't do this, Eloquent breaks on this migration.
     *
     * @var string
     */
    protected $primaryKey = 'product_id';

    /**
     * Attributes that are mass assignable.
     * Maps perfectly to your migration's column names.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'category_id',
        'brand_id',
        'user_id',
        'product_status',
    ];

    /**
     * Cast custom column types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relationship: A product belongs to a category.
     * Because your foreign key is custom ('category_id' pointing to 'category_id'), 
     * you must explicitly pass the foreign and owner keys if they don't follow 'category_id' -> 'id'.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relationship: A product belongs to a brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    /**
     * Relationship: A product belongs to a creator/user.
     * Note: Your migration maps 'user_id' to 'id', so standard convention works here.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(
            color::class,        // 1. The related model you are fetching
            'product_colors',    // 2. The exact same pivot table name
            'product_id',        // 3. Foreign key on pivot referencing THIS model (Product)
            'color_id'           // 4. Foreign key on pivot referencing the RELATED model (Color)
        );
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(
            size::class,         // 1. The related model you are fetching
            'product_sizes',     // 2. The exact same pivot table name
            'product_id',        // 3. Foreign key on pivot referencing THIS model (Product)
            'size_id'            // 4. Foreign key on pivot referencing the RELATED model (Size)
        );
    }

    public function images(): HasMany
    {
        // Eager load sorted by default to save yourself headaches later
        return $this->hasMany(product_image::class, 'product_id', 'product_id')
                    ->orderBy('sort_order', 'asc');
    }

}