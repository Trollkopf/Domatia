@php
    $canPublishProperties = auth()->user()?->canPublishProperties();
@endphp

<table class="table table-hover align-middle m-0">
    <thead class="table-dark">
        <tr>
            @if ($canPublishProperties)
                <th class="text-center" style="width: 48px;">
                    <input type="checkbox" class="form-check-input" data-select-page aria-label="Seleccionar propiedades de esta página">
                </th>
            @endif
            <th>Imagen</th>
            <th>Referencia</th>
            <th>Tipo</th>
            <th>Zona</th>
            <th>Estado</th>
            <th>Ubicacion</th>
            <th>Precio</th>
            <th>Destacada</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($properties as $property)
            <tr data-property-row="{{ $property->id }}">
                @if ($canPublishProperties)
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" value="{{ $property->id }}" data-property-select aria-label="Seleccionar {{ $property->title }}" @disabled(($property->status ?? 'draft') !== 'draft')>
                    </td>
                @endif
                <td style="width: 100px;">
                    <img
                        src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}"
                        class="img-thumbnail"
                        style="width: 80px;"
                    >
                </td>
                <td>{{ $property->ref ?? '-' }}</td>
                <td>{{ $property->tipo ? ucfirst($property->tipo) : '-' }}</td>
                <td>{{ $property->zona->nombre ?? '-' }}</td>
                <td>
                    @php
                        $statusClasses = [
                            'draft' => 'bg-secondary',
                            'published' => 'bg-success',
                            'reserved' => 'bg-warning text-dark',
                            'sold' => 'bg-dark',
                            'hidden' => 'bg-light text-dark',
                        ];
                    @endphp
                    <span class="badge {{ $statusClasses[$property->status ?? 'draft'] ?? 'bg-secondary' }}" data-property-status-badge>
                        {{ $statuses[$property->status ?? 'draft'] ?? ucfirst($property->status ?? 'draft') }}
                    </span>
                </td>
                <td>{{ $property->location ?? '-' }}</td>
                <td>{{ $property->price ? number_format($property->price, 0, ',', '.') . ' EUR' : '-' }}</td>
                <td>
                    <span class="badge {{ $property->is_featured ? 'bg-success' : 'bg-secondary' }}" data-property-featured-badge>
                        {{ $property->is_featured ? 'Si' : 'No' }}
                    </span>
                </td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-2 flex-wrap">
                        @if ($canPublishProperties)
                            <form action="{{ route('admin.properties.quick-update', $property) }}" method="POST" class="d-inline" data-property-quick-update data-update-kind="featured">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="toggle_featured" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-success" data-featured-button>
                                    {{ $property->is_featured ? 'Quitar destacada' : 'Destacar' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.properties.quick-update', $property) }}" method="POST" class="d-inline" data-property-quick-update data-update-kind="status">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" data-status-select aria-label="Cambiar estado de {{ $property->title }}">
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected(($property->status ?? 'draft') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <span class="badge text-bg-light border">Solo edicion</span>
                        @endif

                        <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-sm btn-outline-primary">
                            Editar
                        </a>
                        <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Seguro que quieres eliminar esta propiedad?')">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $canPublishProperties ? 10 : 9 }}" class="text-center py-4">No hay propiedades con los filtros actuales.</td>
            </tr>
        @endforelse
    </tbody>
</table>
