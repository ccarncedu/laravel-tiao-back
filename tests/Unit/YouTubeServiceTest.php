<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\YouTubeService;

class YouTubeServiceTest extends TestCase
{
    public function test_get_video_stats_invalid_url()
    {
        $url = 'https://www.invalidurl.com/watch?v=invalid';

        $service = new YouTubeService();
        $stats = $service->getVideoStats($url);

        $this->assertNull($stats);
    }

}