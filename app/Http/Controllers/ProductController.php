<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        $response = Http::withOptions(['verify' => false])->get('https://api.escuelajs.co/api/v1/products');
        $products = $response->json();
        
        $categories = collect($products)->pluck('category')->unique('id')->values();
        $categoryCounts = collect($products)->groupBy('category.id')->map->count();

        return view('index', compact('categories', 'products', 'categoryCounts'));
    }

    public function getProductsByCategory($id)
    {   
        $response = Http::withOptions(['verify' => false])->get('https://api.escuelajs.co/api/v1/products');
        if($id=='all'){
            $products = collect($response->json())->sortBy('price')->take(10)->values();
        }else{
            $products = collect($response->json())->filter(function ($product) use ($id) {
                return $product['category']['id'] == $id;
            })->sortBy('price')->take(10)->values();
        }
        return response()->json($products);
    }
}
