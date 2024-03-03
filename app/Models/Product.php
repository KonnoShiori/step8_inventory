<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'products';

    // 可変項目
    protected $fillable =
    [
        'company_id',
        'product_name',
        'price',
        'stock',
        'comment',
        'img_path',
        'created_at',
        'updated_at'

    ];

    // リレーション
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // リレーション
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // 商品一覧画面を表示する
    public function index()
    {
        $products = Product::with('company')
            ->get();

        return $products;
    }


    // 商品一覧画面の検索結果を表示する
    public function filterIndex($data)
    {
        $search = $data->search;
        $company_filter = $data->company_filter;
        $lower_price = $data->lower_price;
        $high_price = $data->high_price;
        $lower_stock = $data->lower_stock;
        $high_stock = $data->high_stock;

        $query = Product::query()->with('company');

        /**
         * 検索条件
         */

        // メーカー名
        if (!is_null($company_filter)) {
            $query->where('company_id', $company_filter);
        }

        // 検索キーワード
        if (!is_null($search)) {
            $query->where(function ($query) use ($search) {
                $query->Where('id', $search)
                    ->orWhere('product_name', 'like', "%$search%");
            });
        }

        // 価格：下限
        if (!is_null($lower_price)) {
            $query->where('price', '>=', $lower_price);
        }

        // 価格：上限
        if (!is_null($high_price)) {
            $query->where('price', '<=', $high_price);
        }

        // 在庫：下限
        if (!is_null($lower_stock)) {
            $query->where('stock', '>=', $lower_stock);
        }

        // 価格：上限
        if (!is_null($high_stock)) {
            $query->where('stock', '<=', $high_stock);
        }


        $products = $query->get();

        return $products;
    }

    // 商品を新規登録する
    public function addProduct($data)
    {

        if (request('img_path')) {
            $img_name = $data->file('img_path')->hashName();
            $img_save_name = date('Ymd_His') . "_" . $img_name;
            $data->file('img_path')->storeAs('public/product_images', $img_save_name);
        } else {
            $img_save_name = "";
        }

        return $this->create([
            'company_id' => $data->company_id,
            'product_name' => $data->product_name,
            'price' => $data->price,
            'stock' => $data->stock,
            'comment' => $data->comment,
            'img_path' => $img_save_name,
        ]);
    }

    // 商品情報を更新する
    public function updateProductId($data, $id)
    {

        $inputs = $data->all();
        $update = Product::find($id);

        $img_save_name = $update->img_path;
        if (request('img_path')) {
            $img_name = $data->file('img_path')->hashName();
            $img_save_name = date('Ymd_His') . "_" . $img_name;
            $data->file('img_path')->storeAs('public/product_images', $img_save_name);
        }

        return $update->fill([
            'company_id' => $inputs['company_id'],
            'product_name' => $inputs['product_name'],
            'price' => $inputs['price'],
            'stock' => $inputs['stock'],
            'comment' => $inputs['comment'],
            'img_path' => $img_save_name
        ])->save();
    }

    // 商品のstockから購入数分をマイナスする更新
    public function subtractStock($product_id, $product_stock_update)
    {
        $product = Product::find($product_id);
        $product->stock = $product_stock_update;
        $product->save();
    }


    // 商品を削除する
    public function deleteProductId($data)
    {
        $id = $data->id;
        $delete_product = Product::find($id);
        $delete_product->delete();
    }
}
