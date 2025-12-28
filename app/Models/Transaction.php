<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'total', 'bayar', 'kembali'];

    // Harus 'items' kalau di controller panggil 'items'
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // WAJIB ADA agar tidak error "relationship [user] not found"
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}