<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GithubRepositories\IndexRequest;
use App\Http\Resources\GithubRespositoryResource;
use App\Models\GithubRepository;

class GithubRepositoryController extends Controller
{
    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $repos = GithubRepository::query()
            ->when($request->user_id, function ($q) use ($request){
                $q->whereUserId($request->user_id);
            })->when($request->search, function ($q) use ($request){
                $q->where('name', 'like', '%' . $request->search. '%');
            })->get();

        return GithubRespositoryResource::collection($repos);
    }
}
