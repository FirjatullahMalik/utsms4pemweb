<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; //panggil model product
use Illuminate\Support\Facades\Validator; //panggil library validator
use App\Models\Category; //panggil model category

class ProductController extends Controller
{
    public function Create(Request $request) {
        $validator = Validator::make($request-> all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'expired_at' => 'required|date',
            'modified_by' => 'required|email'
        ]);

        if($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $payload = $validator->validated();

        $thumbnail = $request->file('image');
        $filename = now()->timestamp."_".$request->image->getClientOriginalName();
        $thumbnail->move('uploads', $filename);

        $category = Category::where('name', $payload['category_id'])->first();

        if (!$category) {
            return response()->json(['msg' => 'Category '.$payload['category_id']. ' not found'])->setStatusCode(404);
        }

        Product::create([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'image' => 'uploads/'.$filename,
            'category_id' => $category->id,
            'expired_at' => $payload['expired_at'],
            'modified_by' => $payload['modified_by']
        ]);

        return response()->json([
            'msg' => 'Products successfully created'
        ], 201);

    }

    public function Show(Request $request) {
        $products = Product::all();

        return response()->json([
            'msg' => 'List of all products',
            'data' => $products
        ], 200);
    }

    public function Update(Request $request, $id) {
        $validator = Validator::make($request-> all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'expired_at' => 'required|date',
            'modified_by' => 'required|email'
        ]);

        if($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $payload = $validator->validated();

        $thumbnail = $request->file('image');
        $filename = now()->timestamp."_".$request->image->getClientOriginalName();
        $thumbnail->move('uploads', $filename);

        $category = Category::where('name', $payload['category_id'])->first();

        if (!$category) {
            return response()->json(['msg' => 'Category '.$payload['category_id']. ' not found'])->setStatusCode(404);
        }

        $products = Product::find($id);

        if($products) {
            $products->update([
                'name' => $payload['name'],
                'description' => $payload['description'],
                'price' => $payload['price'],
                'image' => 'uploads/'.$filename,
                'category_id' => $category->id,
                'expired_at' => $payload['expired_at'],
                'modified_by' => $payload['modified_by']
            ]);

            return response()->json([
                'msg' => 'Product with ID: '.$id. ' successfully updated'
            ], 200);

        } else {
            return response()->json([
                'msg' => 'Product with ID: '.$id. ' not found'
            ], 404);
        }
    }

    public function Delete(Request $request, $id) {
        $products = Product::find($id);

        if($products) {
            $products->delete();

            return response()->json([
                'msg' => 'Product with ID: '.$id.' successfully deleted'
            ], 200);
        } else {
            return response()->json([
                'msg' => 'Product with ID: '.$id.' not found'
            ], 404);
        }
    }
}