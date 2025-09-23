<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $items = Product::where('user_id', $user->id)->orderByDesc('id')->get();
        // Decorate image urls
        $items->transform(function($p){
            if ($p->image_path) {
                $p->image_path = Storage::disk('public')->url($p->image_path);
            }
            return $p;
        });
        return $items;
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric','min:0'],
            'price_discount' => ['nullable','numeric','min:0'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'subcategory_id' => ['nullable','integer','exists:sub_categories,id'],
            'brand' => ['nullable','string','max:255'],
            'color' => ['nullable','string','max:255'],
            'unit' => ['nullable','string','max:255'],
            'size' => ['nullable','string','max:255'],
            'sku' => ['nullable','string','max:255'],
            'stock_qty' => ['nullable','integer','min:0'],
            'min_order_qty' => ['nullable','integer','min:1'],
            'seo_title' => ['nullable','string','max:255'],
            'seo_description' => ['nullable','string'],
            'seo_keywords' => ['nullable','string'],
            'image' => ['nullable','image','max:4096']
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $product = Product::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'image_path' => $imagePath,
            'price_discount' => $data['price_discount'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'brand' => $data['brand'] ?? null,
            'color' => $data['color'] ?? null,
            'unit' => $data['unit'] ?? null,
            'size' => $data['size'] ?? null,
            'sku' => $data['sku'] ?? null,
            'stock_qty' => $data['stock_qty'] ?? 0,
            'min_order_qty' => $data['min_order_qty'] ?? 1,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
        ]);
        if ($product->image_path) {
            $product->image_path = Storage::disk('public')->url($product->image_path);
        }
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $product = Product::where('user_id', $user->id)->findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['sometimes','numeric','min:0'],
            'price_discount' => ['nullable','numeric','min:0'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'subcategory_id' => ['nullable','integer','exists:sub_categories,id'],
            'brand' => ['nullable','string','max:255'],
            'color' => ['nullable','string','max:255'],
            'unit' => ['nullable','string','max:255'],
            'size' => ['nullable','string','max:255'],
            'sku' => ['nullable','string','max:255'],
            'stock_qty' => ['nullable','integer','min:0'],
            'min_order_qty' => ['nullable','integer','min:1'],
            'seo_title' => ['nullable','string','max:255'],
            'seo_description' => ['nullable','string'],
            'seo_keywords' => ['nullable','string'],
            'image' => ['nullable','image','max:4096']
        ]);
        if ($request->hasFile('image')) {
            if ($product->image_path) Storage::disk('public')->delete($product->image_path);
            $product->image_path = $request->file('image')->store('products', 'public');
        }
        foreach (['name','description','price','price_discount','category_id','subcategory_id','brand','color','unit','size','sku','stock_qty','min_order_qty','seo_title','seo_description','seo_keywords'] as $f) {
            if (array_key_exists($f, $data)) $product->{$f} = $data[$f];
        }
        $product->save();
        if ($product->image_path) {
            $product->image_path = Storage::disk('public')->url($product->image_path);
        }
        return response()->json($product);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'provider') return response()->json(['message'=>'Forbidden'], 403);
        $product = Product::where('user_id', $user->id)->findOrFail($id);
        if ($product->image_path) Storage::disk('public')->delete($product->image_path);
        $product->delete();
        return response()->json(['success'=>true]);
    }
}
