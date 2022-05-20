<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_to = $request->input('price_to');
        $price_from = $request->input('price_from');

        if ($id) {
            $products =  Product::with(['category', 'galleries'])->find($id);

            if ($products) {
                return ResponseFormatter::success($products, 'Data produk berhasil diambil');
            } else {
                return ResponseFormatter::error(null, 'Data produk tidak ada', 404);
            }
        }

        $products =  Product::with(['category', 'galleries']);

        if ($name) {
            $products->where('name', 'like', '%' . $name . '%');
        }

        if ($description) {
            $products->where('description', 'like', '%' . $description . '%');
        }

        if ($tags) {
            $products->where('tags', 'like', '%' . $tags . '%');
        }

        if ($price_from) {
            $products->where('price', '>=', $price_from);
        }

        if ($price_to) {
            $products->where('price', '<=', $price_to);
        }

        if ($categories) {
            $products->where('categories_id', $categories);
        }

        return ResponseFormatter::success(
            $products->paginate($limit),
            'Data produk berhasil diambil'
        );
    }
}
