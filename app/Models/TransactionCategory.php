<?php
// TODO: Hapus aja karena hasil copy dari project lain
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionCategory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
