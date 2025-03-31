<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    // Definiowanie pól, które mogą być masowo przypisywane
    protected $fillable = ['title', 'content', 'category_id'];

    /**
     * Relacja: kategoria artykułu
     */
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class)->withDefault();
    }
}
