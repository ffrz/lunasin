<?php
// TODO: Hapus aja karena hasil copy dari project lain
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];
}
