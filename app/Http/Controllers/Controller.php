<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //DB functions
    public function getProductsIdByCategoryId($categoryId){
        $productIds= DB::select('select distinct product_id from ec_product_category_product where category_id= ?', [$categoryId]);
        return $productIds;
    }
    public function getProductsByProductIds($productIds){
        $products= array();
        for($i= 0; $i< count($productIds); $i++){
            $product_id= $productIds[$i]->product_id;
            $product= DB::select('select * from ec_products where id= ?', [$product_id]);
            if(count($product)> 0){
                array_push($products, $product[0]);
            }
            
        }
        return $products;
    }


    //request handlers
    public function getAllproducts(){
        var_dump('okkk');
        $products= DB::select('select* from ec_products where quantity> ?', [0]);
        return response()->json($products);
    }

    public function getProductsByCategoryId(Request $request){
        $categoryId= $request->input('categoryId');
        $productIds= $this->getProductsIdByCategoryId($categoryId);
        $products= $this->getProductsByProductIds($productIds);
        return response()->json($products);
    }
    public function getAllCategories(){
        $categories= DB::select('select* from ec_product_categories where id> ?', [0]);
        return response()->json($categories);
    }
}
