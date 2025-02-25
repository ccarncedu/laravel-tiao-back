<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        return Link::paginate(5); 
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'url' => 'required|url',
        ]);

        $link = Link::create([
            'title' => $request->title,
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
            'title' => 'sometimes|string',
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
