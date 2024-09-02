<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use DB;

class TypeController extends  BaseController
{
    const ITEM_PER_PAGE = 25;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $typeQuery = Type::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);

        return TypeResource::collection($typeQuery->paginate($limit));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function show(Type $type)
    {
        $result = Type::query()->select('id', 'name', 'color')->where('status', '=', true)->get();
        
        return TypeResource($result);
    }

    public function getTypes(Request $request)
    {
        $searchParam = $request->all();
    
        $typeQuery = Type::query()->select('id', 'name', 'color', 'status')->where('status', '=', true)->get();
        
        return $typeQuery;
    }

    public function store(Request $request)
    {
        $params = $request->all();
        $currentUser = Auth::user();
        $type = Type::create([
            'name'=> $params['name'],
            'color'=> $params['color'],
            'has_hours'=> $params['has_hours'],
            'status'=> $params['status'],
        ]);

        return new TypeResource($type);
    }

    public function update(Request $request, Type $type)
    {
        $params = $request->all();
        $currentUser = Auth::user();
        $type->update([
            'name'=> $params['name'],
            'color'=> $params['color'],
        ]);
        
        return new TypeResource($type);
    }

    public function destroy(Type $type)
    {
        $deleted = false;
        try {
            $deleted = $type->delete();
        } catch(\Exception $ex){
            response()->json(['error' => $ex->getMessage()], 403);
        }

        return $deleted;
    }

    public function updateStatus(Request $request)  
    {
        $params = $request->all(); 
        $statusQuery = DB::table('types')->where('types.id', '=', $params['id'])->update(['status' => $params['status']]);

        return $statusQuery;
    }
}
