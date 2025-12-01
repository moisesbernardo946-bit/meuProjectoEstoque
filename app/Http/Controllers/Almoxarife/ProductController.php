<?php

namespace App\Http\Controllers\Almoxarife;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $products = Product::with(['category', 'unit', 'zone'])
            ->when($search, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        $totalProducts = Product::count();

        if ($request->ajax()) {
            return view('almoxarife.products.table', compact('products'))->render();
        }

        return view('almoxarife.products.index', compact('products', 'totalProducts', 'search'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('almoxarife.products.create', compact('categories', 'units', 'zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'measure' => 'required|string|max:200',
            'zone_id' => 'required|exists:zones,id',
        ]);

        // Verifica duplicação
        $exists = Product::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('unit_id', $request->unit_id)
            ->whereRaw('LOWER(measure) = ?', [strtolower($request->measure)])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Já existe um produto com este nome, unidade e medida.'])
                ->withInput();
        }

        $code = strtoupper(Str::random(10));

        $product = Product::create([
            'name' => $request->name,
            'code' => $code,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'measure' => $request->measure,
            'zone_id' => $request->zone_id,
        ]);

        // Dados para o QR
        $qrData = "Produto: {$product->name}\nCódigo: {$product->code}\nCategoria: {$product->category->name}\nMedida: {$product->measure}\nZona: {$product->zone->name}\nUnidade: {$product->unit->symbol}";

        $builder = new Builder(writer: new PngWriter(), data: $qrData, size: 250, margin: 10);
        $result = $builder->build();

        // Pasta de armazenamento persistente
        $qrFolder = storage_path('app/public/qrcodes');
        if (!File::exists($qrFolder)) {
            File::makeDirectory($qrFolder, 0775, true);
        }

        $fileName = "{$product->code}_" . time() . ".png";
        file_put_contents($qrFolder . '/' . $fileName, $result->getString());

        // Armazena caminho acessível publicamente
        $product->update(['qr_code_path' => "storage/qrcodes/{$fileName}"]);

        return redirect()
            ->route('almoxarife.products.index')
            ->with('success', '✅ Produto cadastrado com sucesso!');
    }

    public function edit(int $id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();

        return view('almoxarife.products.edit', compact('product', 'categories', 'units', 'zones'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'measure' => 'required|string|max:200',
            'zone_id' => 'required|exists:zones,id',
        ]);

        $product = Product::findOrFail($id);

        // Verifica duplicação
        $exists = Product::whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->where('unit_id', $request->unit_id)
            ->whereRaw('LOWER(measure) = ?', [strtolower($request->measure)])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Já existe outro produto com este nome, unidade e medida.'])
                ->withInput();
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'measure' => $request->measure,
            'zone_id' => $request->zone_id,
        ]);

        // Dados do QR atualizado
        $qrData = "Produto: {$product->name}\nCódigo: {$product->code}\nCategoria: {$product->category->name}\nMedida: {$product->measure}\nZona: {$product->zone->name}\nUnidade: {$product->unit->symbol}";

        $builder = new Builder(writer: new PngWriter(), data: $qrData, size: 250, margin: 10);
        $result = $builder->build();

        // Apaga QR antigo
        if ($product->qr_code_path && File::exists(public_path($product->qr_code_path))) {
            File::delete(public_path($product->qr_code_path));
        }

        // Salva novo QR
        $qrFolder = storage_path('app/public/qrcodes');
        if (!File::exists($qrFolder)) {
            File::makeDirectory($qrFolder, 0775, true);
        }

        $fileName = "{$product->code}_" . time() . ".png";
        file_put_contents($qrFolder . '/' . $fileName, $result->getString());

        $product->update(['qr_code_path' => "storage/qrcodes/{$fileName}"]);

        return redirect()
            ->route('almoxarife.products.index')
            ->with('success', '✅ Produto atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        if ($product->qr_code_path && File::exists(public_path($product->qr_code_path))) {
            File::delete(public_path($product->qr_code_path));
        }

        $product->delete();

        return redirect()
            ->route('almoxarife.products.index')
            ->with('success', 'Produto removido com sucesso!');
    }
}
