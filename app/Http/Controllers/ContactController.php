<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:1000',
            'property_id' => 'nullable|exists:properties,id',
            'accept_terms' => 'sometimes|accepted',
        ]);

        Contacto::create([
            'nombre' => $request->name,
            'email' => $request->email,
            'telefono' => $request->phone,
            'mensaje' => $request->message,
            'property_id' => $request->property_id,
        ]);

        return redirect()->back()->with('success', __('ui.contact.success'));
    }
}
