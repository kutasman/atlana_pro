<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GithubUsers\IndexRequest;
use App\Http\Resources\UserFullResource;
use App\Http\Resources\UserResource;
use App\Jobs\SyncGithubUsersData;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\GithubRepository;
use Illuminate\Support\Facades\DB;

class GithubUsersController extends Controller
{
    /**
     * @param IndexRequest $request
     * @param GithubRepository $repo
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request, GithubRepository $repo)
    {
        $data = $repo->usersSearch($request->search, $request->endCursor, $request->startCursor);

        if ($users = data_get($data, 'users')){
            $githubUsers = collect();

            foreach ($users as $userData){

                DB::beginTransaction();

                try {
                    //create or updated users
                    /** @var User $user */
                    $user = User::updateOrCreate(
                        ['github_db_id' => $userData['databaseId']],
                        [
                            'name' => $userData['name'],
                            'github_repositories_count' => data_get($userData, 'repositories.totalCount'),
                            'github_subscribers_count' => data_get($userData, 'followers.totalCount'),
                            'location' => $userData['location'],
                            'created_at' => $userData['createdAt'],
                            'avatar_url' => $userData['avatarUrl'],
                            'bio' => $userData['bio'],
                        ]
                    );

                    //handle repositories relation sync
                    if ( $repositories = collect(data_get($userData, 'repositories.edges'))->pluck('node')){
                        foreach ($repositories as $repoData){
                            $user->repositories()->updateOrCreate(
                                ['github_db_id' => data_get($repoData, 'databaseId')],
                                [
                                    'name' => $repoData['name'],
                                    'forks_count' => $repoData['forkCount'],
                                    'stars_count' => $repoData['stargazerCount'],
                                ]
                            );
                        }
                    }

                    $githubUsers->push($user);

                    DB::commit();
                } catch (\Exception $exception){
                    //handle error
                    DB::rollBack();
                    logger($exception->getMessage());
                }
            }
            $responseData = [
                'users' => UserResource::collection($githubUsers
                    ->sortBy(['github_repositories_count' => 'desc', 'github_subscribers_count' => 'desc', 'profile_shown_counter' => 'desc'])
                    ->values()),
            ];

        } else {
            $responseData = ['users' => []];
        }

        $responseData = array_merge($responseData, [
            'pageInfo' => $data['pageInfo'],
            'userCount' => $data['userCount']
        ]);

        return response()->json( ['data' => $responseData] );
    }

    /**
     * @param Request $request
     * @param User $user
     * @return UserFullResource
     */
    public function show(User $user)
    {
        $user->increment('profile_shown_counter');

        return UserFullResource::make($user);
    }
}
