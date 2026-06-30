<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Propietario;
use Illuminate\Http\Request;

class PropietarioController extends Controller
{
    public function search(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $query = Propietario::query()->orderBy('nombre');

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'results' => $query->limit(15)->get(['id', 'nombre', 'telefono', 'email'])->map(fn (Propietario $propietario) => [
                'id' => $propietario->id,
                'label' => $propietario->nombre,
                'contact' => $propietario->telefono ?: $propietario->email,
            ]),
        ]);
    }

    public function index(Request $request)
    {
        $query = Propietario::query()->withCount('properties')->orderBy('nombre');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $propietarios = $query->paginate(15)->withQueryString();

        return view('admin.propietarios.index', compact('propietarios'));
    }

    public function store(Request $request)
    {
        $propietario = Propietario::create($request->validate($this->rules()));

        return redirect()->route('admin.propietarios.edit', $propietario)
            ->with('success', 'Propietario creado correctamente.');
    }

    public function edit(Propietario $propietario)
    {
        $properties = $propietario->properties()
            ->with('zona')
            ->latest()
            ->paginate(10);

        return view('admin.propietarios.edit', compact('propietario', 'properties'));
    }

    public function update(Request $request, Propietario $propietario)
    {
        $propietario->update($request->validate($this->rules()));

        return redirect()->route('admin.propietarios.edit', $propietario)
            ->with('success', 'Datos del propietario actualizados.');
    }

    public function destroy(Propietario $propietario)
    {
        $propertyCount = $propietario->properties()->count();
        $propietario->properties()->update(['propietario_id' => null]);
        $propietario->delete();

        $message = 'Propietario eliminado.';

        if ($propertyCount > 0) {
            $message .= " {$propertyCount} propiedades han quedado sin propietario asignado.";
        }

        return redirect()->route('admin.propietarios.index')->with('success', $message);
    }

    private function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string|max:5000',
        ];
    }
}
