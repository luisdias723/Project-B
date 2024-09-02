<?php

namespace App\Http\Controllers\Api;

use App\Models\Checklist;
use Illuminate\Http\Request;
use App\Http\Resources\ChecklistResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChecklistController extends BaseController
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
        $keyword = Arr::get($searchParams, 'keyword', ''); 
        $checklistQuery = Checklist::query()->select('id', 'name', 'created_at');

        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        
        // search by keyword
        if (!empty($keyword)) {
            $checklistQuery->where(function($query) use ($keyword){
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
            });
        }
        
        

        return ChecklistResource::collection($checklistQuery->paginate($limit));
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
        $currentUser = auth()->user()->id;
        
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
            ]
        );

        $checklist = Checklist::create([
            'name' => $params['name'],
            'created_by' => $currentUser,
        ]);
        
        return new ChecklistResource($checklist);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function show(Checklist $checklist)
    {

        $result = Checklist::query()->where('id', $checklist->id)->first();
        //$result['questions'] = Question::where('checklist_id', $checklist->id)->get();

        return new ChecklistResource($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function edit(Checklist $checklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Checklist $checklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\Checklist  $checklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checklist $checklist)
    {

        $currentUser = Auth::user();

        $roles = $currentUser->getRoleNames()->toArray();

        if (!in_array(['admin'], $roles)){
            response()->json(['error' => 'Não foi possível concluír o pedido.'], 403);
        }

        try {
            $checklist->delete();
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);

    }

}
