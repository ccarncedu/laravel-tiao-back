<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YouTubeService
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('YOUTUBE_API_KEY');
    }

    public function getVideoStats($url)
    {
        $videoId = $this->extractVideoId($url);

        if (!$videoId) {
            return null;
        }

        $response = Http::withOptions(['verify' => false])->get("https://www.googleapis.com/youtube/v3/videos", [
            'id' => $videoId,
            'part' => 'snippet,statistics',
            'key' => $this->apiKey,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (empty($data['items'])) {
            return null;
        }

        $video = $data['items'][0];

        return [
            'title' => $video['snippet']['title'] ?? '',
            'thumbnail' => $video['snippet']['thumbnails']['high']['url'] ?? '',
            'views' => $video['statistics']['viewCount'] ?? 0,
            'likes' => $video['statistics']['likeCount'] ?? 0,
        ];
    }

    private function extractVideoId($url)
    {
        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:.*v=|.*\/|embed\/|v\/))([\w-]+)/', $url, $matches);
        return $matches[1] ?? null;
    }
}