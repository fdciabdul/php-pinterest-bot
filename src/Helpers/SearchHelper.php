<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Response;

trait SearchHelper
{
    use PaginationHelper;

    /**
     * @var Response;
     */
    protected $moduleSearchPage = "SearchPage";

    /**
     * Executes search to API. Query - search string.
     *
     * @param string $query
     * @param string $scope
     * @param array  $bookmarks
     * @return array
     */
    public function searchCall($query, $scope, $bookmarks = [])
    {
        $url = UrlHelper::getSearchUrl(! empty($bookmarks));
        $get = $this->createSearchRequest($query, $scope, $bookmarks);
        $url = $url.'?'.UrlHelper::buildRequestString($get);
        $response = $this->request->exec($url);
        return $this->response->parseSearchResponse($response, !empty($bookmarks));
    }

    /**
     * Executes search to API with pagination.
     *
     * @param string $query
     * @param string $scope
     * @param int    $batchesLimit
     * @return \Iterator
     */
    public function searchWithPagination($query, $scope, $batchesLimit)
    {
        return $this->getPaginatedData(
            [$this, 'searchCall'], [
            'query' => $query,
            'scope' => $scope,
        ], $batchesLimit
        );
    }

    /**
     * Creates Pinterest API search request
     *
     * @param       $query
     * @param       $scope
     * @param array $bookmarks
     * @return array
     */
    public function createSearchRequest($query, $scope, $bookmarks = [])
    {
        $options = [
            "scope" => $scope,
            "query" => $query,
        ];

        $dataJson = ["options" => $options];

        if ( ! empty($bookmarks)) {
            $dataJson['options']['bookmarks'] = $bookmarks;
        } else {
            $dataJson = array_merge(
                $dataJson, [
                'module' => [
                    "name"    => $this->moduleSearchPage,
                    "options" => $options,
                ],
            ]
            );
        }

        return RequestHelper::createRequestData(
            $dataJson, "/search/$scope/?q=".$query
        );
    }
}
