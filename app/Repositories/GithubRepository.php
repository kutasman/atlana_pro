<?php

namespace App\Repositories;

use GrahamCampbell\GitHub\Facades\GitHub;

class GithubRepository
{

    /**
     * @param $search
     * @param string|null $endCursor
     * @param string|null $startCursor
     * @return array
     * @throws \Exception
     */
    public function usersSearch($search, ?string $endCursor = null, ?string $startCursor = null)
    {
        $cursor = $this->normalizeCursor($endCursor, $startCursor);
        $query = '{
          search(type:USER, query:"'. $search . ' in:name in:email in:login", ' . $cursor . '){
            userCount
            pageInfo {
                endCursor
                startCursor
                hasNextPage
                hasPreviousPage
            }
            edges{
              cursor
              node{
                ... on User {
                  databaseId
                  name
                  location
                  createdAt
                  bio
                  email
                  login
                  avatarUrl
                  repositories(first:100){
                    totalCount
                    edges{
                      node{
                        ... on Repository{
                          databaseId
                          name
                          forkCount
                          stargazerCount
                        }
                      }
                    }
                  }
                  followers{
                    totalCount
                  }
                  following{
                    totalCount
                  }
                }
              }
            }
          }
        }';

        $response = GitHub::api('graphql')->execute($query);

        return [
            'users' => collect(data_get($response,'data.search.edges', []))->map(fn($node) => $node['node'] ),
            'pageInfo' => $this->normalizePageInfo(data_get($response,'data.search.pageInfo')),
            'userCount' => data_get($response,'data.search.userCount'),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizePageInfo(array $data): array
    {
        return [
            'endCursor' => data_get($data, 'hasNextPage') ? data_get($data, 'endCursor') : false,
            'hasNextPage' => data_get($data, 'hasNextPage', false),
            'startCursor' => data_get($data, 'hasPreviousPage') ? data_get($data, 'startCursor') : false,
            'hasPreviousPage' => data_get($data, 'hasPreviousPage', false),
        ];

    }

    /**
     * @param string|null $endCursor
     * @param string|null $startCursor
     * @return string
     * @throws \Exception
     */
    private function normalizeCursor(?string $endCursor = null, ?string $startCursor = null):string
    {
        logger([$endCursor, $startCursor]);
        if ($endCursor && $startCursor){ throw new \Exception('Only one cursor type must be provided');}

        if (is_string($endCursor)){
            return 'first:3 after:"' . $endCursor .'"';
        }

        if (is_string($startCursor)){
            return 'last:3 before:"' . $startCursor . '"';
        }

        return 'first:3';
    }
}
