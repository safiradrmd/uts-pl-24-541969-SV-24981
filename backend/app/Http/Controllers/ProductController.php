<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Tampil Semua Data (Index)
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status'  => 'success',
            'data'    => $products
        ], 200);
    }

    // Tambah Data Baru (Store) -> HTTP 201 & 422
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422); // Status 422 Validasi Gagal
        }

        $product = Product::create($request->all());

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'data'    => $product
        ], 201); // Status 201 Created
    }

    // Tampil Satu Data (Show)
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $product
        ], 200);
    }

    // Ubah Data (Update)
    public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'name'  => 'sometimes|required|string|max:255',
        'price' => 'sometimes|required|numeric|min:0',
        'stock' => 'sometimes|required|integer|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    $product->update($request->all());

    return response()->json([
        'status'  => 'success',
        'message' => 'Produk berhasil diperbarui',
        'data'    => $product
    ], 200);
}

    // Hapus Data (Destroy)
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }
}
