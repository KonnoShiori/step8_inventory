<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;
    // テーブル名
    protected $table = 'sales';

    // 可変項目
    protected $fillable =
    [
        'product_id',
        'purchase_quantity',
        'created_at',
        'updated_at'
    ];

    // リレーション
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 商品購入情報を登録する
    public function addSale($product_id, $purchase_quantity)
    {
        return $this->create([
            'product_id' => $product_id,
            'purchase_quantity' => $purchase_quantity
        ]);
    }
}
