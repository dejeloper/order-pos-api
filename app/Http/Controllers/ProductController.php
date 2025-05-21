<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Productos habilitados
    public function index()
    {
        $products = Product::where('enabled', true)->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No enabled products found'], 200);
        }

        return response()->json($products, 200);
    }

    // Productos deshabilitados
    public function indexDisabled()
    {
        $products = Product::onlyTrashed()->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No disabled (soft-deleted) products found'], 200);
        }

        return response()->json($products, 200);
    }

    // Obtener producto por ID (solo habilitados)
    public function show($id)
    {
        $product = Product::where('enabled', true)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found or not enabled'], 404);
        }

        return response()->json($product, 200);
    }

    // Obtener producto solo si está soft-deleted
    public function showTrashed($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return response()->json(['message' => 'Deleted product not found'], 404);
        }

        return response()->json($product, 200);
    }

    // Crear producto
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:100',
            'price' => 'required|numeric|min:0.01|max:10000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name' => $request->get('name'),
            'price' => $request->get('price'),
            'enabled' => true,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    // Actualizar producto habilitado
    public function update(Request $request, $id)
    {
        $product = Product::where('enabled', true)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Cannot update: product not found or disabled'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|min:3|max:100',
            'price' => 'sometimes|numeric|min:0.01|max:10000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update([
            'name' => $request->get('name', $product->name),
            'price' => $request->get('price', $product->price)
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
        ], 200);
    }

    // Soft delete (deshabilita)
    public function destroy($id)
    {
        $product = Product::where('enabled', true)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found or already deleted'], 404);
        }

        $product->enabled = false;
        $product->save();
        $product->delete(); // soft delete

        return response()->json(['message' => 'Product disabled and soft deleted'], 200);
    }

    // Restaurar producto soft deleted
    public function restore($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return response()->json(['message' => 'Deleted product not found'], 404);
        }

        $product->restore();
        $product->enabled = true;
        $product->save();

        return response()->json(['message' => 'Product restored successfully'], 200);
    }

    // Borrado permanente
    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return response()->json(['message' => 'Deleted product not found'], 404);
        }

        $product->forceDelete();

        return response()->json(['message' => 'Product permanently deleted'], 200);
    }
}