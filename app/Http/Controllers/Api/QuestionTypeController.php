<?php

namespace App\Http\Controllers\Api;


use App\Models\QuestionType;
use Illuminate\Http\Request;
use App\Http\Resources\QuestionTypeResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuestionTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getQuestionType(Request $request){

        $searchParams = $request->all();
        
        $query = QuestionType::query()->select('id','name');

        return QuestionTypeResource::collection($query->get());
        
    }
}
