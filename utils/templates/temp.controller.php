<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $campus = Campus::latest()->paginate(10);
        return response()->json([
            "data" => $campus
        ],
        200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [ 'name' => 'required' ]
        );
        
        if($validator->fails()){
            return response()->json(
                ['messages' => $validator->errors() ],
                403
            );
        }
 
        $campus = Campus::create($request->all());
        return response()->json([
            "data" => $campus
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Campus $campus)
    {
        return response()->json([
                "data" => $campus
            ], 
            200
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Campus $campus)
    {
        $validator = Validator::make($request->all(),
            [ 'name' => 'required' ]
        );
        
        if($validator->fails()){
            return response()->json(
                ['messages' => $validator->errors() ],
                403
            );
        }
        $campus->update($request->all());
 
        return response()->json([
                "data" => $campus,
                "msg" => "Updated successfully"
            ], 
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Campus $campus)
    {
        $campus->delete();
        return response()->json(
            [
                "data" => $campus,
                "msg" => "Deleted successfully"
            ]
            , 202); 
    }
}
