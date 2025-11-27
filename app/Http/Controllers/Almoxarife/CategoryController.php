<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $categories = Category::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('almoxarife.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('almoxarife.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($data);

        return redirect()
            ->route('almoxarife.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(Category $category)
    {
        return view('almoxarife.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect()
            ->route('almoxarife.categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('almoxarife.categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
