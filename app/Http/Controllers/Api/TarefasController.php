<?php

namespace App\Http\Controllers\Api;

use App\Models\Tarefas;
use Illuminate\Http\Request;
use App\Http\Resources\TarefasResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TarefasController extends BaseController
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

        $TarefasQuery = Tarefas::query()->select('id','name','data_inicio', 'data_limite');

        if (!empty($keyword)) {
            $TarefasQuery->where(function($query) use ($keyword){
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
            });
        }


        return TarefasResource::collection($TarefasQuery->paginate($limit));
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
       
        $tarefa= Tarefas::create([
            'name' => $params['name'],
            'user_id' => $params['user_id'],
            'checklist_id' => $params['checklist_id'],
            'data_inicio'=> $params['data_inicio'],
            'data_limite'=> $params['data_limite'],

        ]);

        return new TarefasResource($tarefa);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Models\Tarefas  $checklist
     * @return \Illuminate\Http\Response
     */
    public function show(Tarefas $tarefa)
    {
        $result= Tarefas::query()->where('id', $tarefa->id)->first();
        return new TarefasResource($result);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Models\Tarefas  $checklist
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarefas $tarefa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Models\Tarefas  $checklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarefas $tarefa)
    {
        $params = $request->all();
        $tarefa->update([
            'name' => $params['name'],
            'user_id' => $params['user_id'],    
            'checklist_id' => $params['checklist_id'],
            
            'data_limite'=> $params['data_limite'],
        ]);

            return new TarefasResource($tarefa);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Models\Tarefas  $checklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarefas $tarefa)
    {
  
        try {
            $tarefa->delete();
        } catch (\Exception $ex) {
            response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }

}
