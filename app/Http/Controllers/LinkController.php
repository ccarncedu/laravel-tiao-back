<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;
use App\Services\YouTubeService;

class LinkController extends Controller
{
    public function index(Request $request, YouTubeService $youtubeService)
    {
        $perPage = $request->query('per_page', 5);

        $query = Auth::user()->is_admin ? Link::query() : Link::where('approved', true);
    
        $links = $query->paginate($perPage);
    
        $links->getCollection()->transform(function ($link) use ($youtubeService) {
            $youtubeData = $youtubeService->getVideoStats($link->url);
    
            return [
                'id' => $link->id,
                'title' => $youtubeData['title'] ?? $link->title,
                'url' => $link->url,
                'thumbnail' => $youtubeData['thumbnail'] ?? null,
                'views' => $youtubeData['views'] ?? 0,
                'likes' => $youtubeData['likes'] ?? 0,
                'user_id' => $link->user_id,
                'is_approved' => (bool) $link->approved 
            ];
        });
    
        return response()->json([
            'data' => $links->items(),
            'current_page' => $links->currentPage(),
            'total_pages' => $links->lastPage(),
            'total_items' => $links->total(),
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $link = Link::create([
            'url' => $request->url,
            'user_id' => Auth::id(),
        ]);

        return response()->json($link);
    }

    public function approve($id)
    {
        $link = Link::findOrFail($id);

        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Ação não permitida.'], 403);
        }

        $link->approved = true;
        $link->save();

        return response()->json($link);
    }

    public function destroy($id)
    {
        $link = Link::findOrFail($id);

        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Ação não permitida.'], 403);
        }

        $link->delete();
        return response()->json(['message' => 'Link excluído com sucesso.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'url' => 'sometimes|url',
            'approved' => 'sometimes|boolean',
        ]);

        $link = Link::findOrFail($id);

        if ($link->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return response()->json(['error' => 'Ação não permitida.'], 403);
        }

        $link->update($request->only(['title', 'url', 'approved']));

        return response()->json($link);
    }

}
