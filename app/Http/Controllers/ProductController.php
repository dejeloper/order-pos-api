<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar productos habilitados",
     *     tags={"Productos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos habilitados",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index()
    {
        $products = Product::where('enabled', true)->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No enabled products found'], 200);
        }

        return response()->json($products, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/disabled",
     *     summary="Listar productos deshabilitados (soft deleted)",
     *     tags={"Productos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos deshabilitados",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function indexDisabled()
    {
        $products = Product::onlyTrashed()->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No disabled (soft-deleted) products found'], 200);
        }

        return response()->json($products, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obtener producto habilitado por ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado o no habilitado"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::where('enabled', true)->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found or not enabled'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/trashed/{id}",
     *     summary="Obtener producto eliminado (soft deleted) por ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto eliminado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Deleted product not found"
     *     )
     * )
     */
    public function showTrashed($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return response()->json(['message' => 'Deleted product not found'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Crear producto",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price"},
     *             @OA\Property(property="name", type="string", example="Producto X"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Actualizar producto habilitado",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Nuevo nombre"),
     *             @OA\Property(property="price", type="number", format="float", example=199.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado o deshabilitado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Deshabilitar (soft delete) producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto deshabilitado y soft deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado o ya eliminado"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/products/{id}/restore",
     *     summary="Restaurar producto eliminado (soft deleted)",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto eliminado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto restaurado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product restored successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Deleted product not found"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/products/{id}/force",
     *     summary="Eliminar producto permanentemente",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto eliminado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado permanentemente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Deleted product not found"
     *     )
     * )
     */
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
