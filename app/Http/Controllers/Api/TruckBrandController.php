<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\TruckBrand;
use App\Http\Resources\TruckBrandResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class TruckBrandController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
     const ITEM_PER_PAGE = 25;

    public function index(Request $request)
    {
        $searchParams = $request->all();
        $keyword = Arr::get($searchParams, 'keyword', ''); 
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $order = Arr::get($searchParams, 'order', 'ASC');

        $TruckBrandsQuery = TruckBrand::query()
        ->select(DB::raw('(SELECT count(m.brand_id)  FROM truck_models m WHERE m.brand_id=truck_brands.id) as total '),'truck_brands.id', 'truck_brands.brand')
        ->orderBy('truck_brands.brand', $order);
      
        if (!empty($keyword)) {
            $TruckBrandsQuery->where(function($query) use ($keyword){
                    $query->where('truck_brands.brand', 'LIKE', '%' . $keyword . '%');
            });
        }

        return TruckBrandResource::collection($TruckBrandsQuery->paginate($limit));
    }
    

    public function getTruckBrands()
    {  

        $TruckBrandquery = TruckBrand::query()->select('id', 'brand');

        return TruckBrandResource::collection($TruckBrandquery  ->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();

        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'brand' => 'required|min:3',
                ]
            )
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {  
            $brand= TruckBrand::create([
                'brand' => $params['brand'],
            ]);
    }

        return new TruckBrandResource($brand);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TruckBrand $truckBrand)
    {
        $result = TruckBrand::query();
        $result->select('id','brand')
        ->where("id",$truckBrand->id);


       /* $result= TruckFleets::find($truckFleet->id);*/
        return $result->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
  

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TruckBrand $truckBrand)
    {
        $params = $request->all();

        
        $validator = Validator::make(
            $request->all(),
            array_merge(
                [
                    'brand' => 'required|min:3',
                ]
            )
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        } else {  
            $truckBrand->update([
                'brand' => $params['brand'],
            ]);
      
        }     

              return new TruckBrandResource($truckBrand);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
