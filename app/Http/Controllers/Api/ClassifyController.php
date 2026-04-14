<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ClassifyController extends Controller
{
    public function classify(Request $request){
        $name = $request->query('name');
        if(!$name){
            return response()->json([
                'status'=>'error',
                'message'=>'The name parameter is required.'
            ],400)->header('Access-Control-Allow-Origin', '*');
        }
        if(!is_string($name)){
            return response()->json([
                'status'=>'error',
                'message'=>'The name parameter must be a string'
            ],422);
        }
        // call external api
        $response = Http::get("https://api.genderize.io", [
            'name'=>$name
        ]);

        if ($response->failed() || !$response->json()){
            return response()->json([
                'status'=>'error',
                'message'=>'Failed to fetch data from the Genderize API'
            ],502)->header('Access-Control-Allow-Origin', '*');
        }

        // convert response to array
        $data = $response->json();

        $gender = $data['gender'];
        $probability = $data['probability'];
        $count = $data['count'];

        if ($gender === null || $count === 0) {
            return response()->json([
                "status" => "error",
                "message" => "No prediction available for the provided name"
            ], 422);
        }

        $is_confident = ($probability >= 0.7 && $count >= 100);

        $processed_at = now()->toISOString();

        return response()->json([
            "status" => "success",
            "data" => [
                "name" => strtolower($name),
                "gender" => $gender,
                "probability" => $probability,
                "sample_size" => $count,
                "is_confident" => $is_confident,
                "processed_at" => $processed_at
            ]
        ], 200)->header('Access-Control-Allow-Origin', '*');
    }
}
