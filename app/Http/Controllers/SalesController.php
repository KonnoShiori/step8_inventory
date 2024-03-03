<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\Company;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * 商品購入の登録
     * @param $request
     * @return
     */
    public function productPurchase(SaleRequest $request)
    {
        $product_id = $request->input('product_id');
        $purchase_quantity = $request->input('purchase_quantity', 1);

        $product = Product::find($product_id);
        // product_idの商品が無い場合
        if (!$product) {
            throw new Exception('商品情報が存在しません');
        }

        // product_idの商品が有る場合は、以下の処理をする
        $product_stock = $product->stock;
        $product_stock_update = $product_stock - $purchase_quantity;

        // 購入数＞在庫の場合
        if ($product_stock_update < 0) {
            throw new Exception('商品の在庫数が足りないため（在庫：' . $product_stock . '）購入処理できません');
        }

        // 購入数≦在庫の場合は、以下の処理をする
        DB::beginTransaction();

        try {
            // Salesデーブルへ登録
            $model = new Sale();
            $add_sale = $model->addSale($product_id, $purchase_quantity);

            // Productsデーブルのstockから購入数分を減らす更新
            $model = new Product();
            $subtract_stock = $model->subtractStock($product_id, $product_stock_update);

            DB::commit();

            $result = [
                'result' => 'success',
                'product_id' => $add_sale->product_id,
                'purchase_quantity' => $add_sale->purchase_quantity,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json($result);
    }
}
