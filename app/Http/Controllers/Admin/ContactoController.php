<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contacto;
use App\Models\Property;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contacto::query()->with('property');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nombre', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->input('property_id'));
        }

        if ($request->filled('follow_up')) {
            if ($request->input('follow_up') === 'due') {
                $query->whereDate('next_action_at', '<=', now()->toDateString());
            }

            if ($request->input('follow_up') === 'scheduled') {
                $query->whereNotNull('next_action_at');
            }
        }

        $contactos = $query->latest()->paginate(15)->withQueryString();
        $properties = Property::orderBy('title')->get(['id', 'title', 'ref']);

        return view('admin.contactos.index', compact('contactos', 'properties'));
    }

    public function show(Contacto $contacto)
    {
        $contacto->load('property');

        return view('admin.contactos.show', compact('contacto'));
    }

    public function update(Request $request, Contacto $contacto)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendiente,contactado,cerrado',
            'next_action_at' => 'nullable|date',
            'internal_notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'contactado' && ! $contacto->last_contacted_at) {
            $validated['last_contacted_at'] = now();
        }

        if ($validated['status'] === 'cerrado') {
            $validated['next_action_at'] = null;
        }

        $contacto->update($validated);

        return redirect()
            ->route('admin.contactos.show', $contacto)
            ->with('success', 'Seguimiento del contacto actualizado.');
    }

    public function quickUpdate(Request $request, Contacto $contacto)
    {
        $validated = $request->validate([
            'status' => 'required|in:pendiente,contactado,cerrado',
            'search' => 'nullable|string',
            'filter_status' => 'nullable|string',
            'property_id' => 'nullable|string',
            'follow_up' => 'nullable|string',
            'page' => 'nullable|string',
        ]);

        $payload = ['status' => $validated['status']];

        if ($validated['status'] === 'contactado') {
            $payload['last_contacted_at'] = now();
        }

        if ($validated['status'] === 'cerrado') {
            $payload['next_action_at'] = null;
        }

        $contacto->update($payload);

        return redirect()
            ->route('admin.contactos.index', [
                'search' => $request->input('search'),
                'status' => $request->input('filter_status'),
                'property_id' => $request->input('property_id'),
                'follow_up' => $request->input('follow_up'),
                'page' => $request->input('page'),
            ])
            ->with('success', 'Estado del contacto actualizado.');
    }
}
