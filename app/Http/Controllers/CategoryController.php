<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function Create(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

         
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

         
        $payload = $validator->validated();

         
        $category = Category::create([
            'name' => $payload['name'],
        ]);

         
        return response()->json([
            'message' => 'Kategori berhasil dibuat',
            'category' => $category
        ], 201);
    }

    public function Show(Request $request)
    {
         
        $categories = Category::all();

         
        return response()->json([
            'message' => 'Data semua kategori',
            'categories' => $categories
        ], 200);
    }

    public function Update(Request $request, $id)
    {
         
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }
 
        $payload = $validator->validated();

         
        $category = Category::findOrFail($id);
        $category->update([
            'name' => $payload['name']
        ]);

         
        return response()->json([
            'message' => 'Kategori berhasil diupdate',
            'category' => $category
        ], 200);
    }

    public function Delete(Request $request, $id)
    {
        
        $category = Category::find($id);

        
        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        
        $category->delete();

        
        return response()->json(['message' => 'Kategori berhasil dihapus'], 200);
    }
}
