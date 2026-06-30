@extends('layouts.admin')

@section('title', 'Editar Propiedad')

@section('styles')
    <style>
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background-color: #f9f9f9;
            transition: background 0.2s ease-in-out;
        }

        .dropzone.dragover {
            background-color: #e3e3e3;
        }

    </style>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h1 class="m-0 fs-4">Editar Propiedad</h1>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.properties._form')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('images');

        if (dropzone && fileInput) {
            const previewContainer = document.createElement('div');
            previewContainer.classList.add('row', 'mt-3');
            dropzone.after(previewContainer);

            fileInput.addEventListener('change', function () {
                showPreviews(this.files);
            });

            dropzone.addEventListener('click', () => fileInput.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('dragover');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                fileInput.files = e.dataTransfer.files;
                showPreviews(e.dataTransfer.files);
            });

            function showPreviews(files) {
                previewContainer.innerHTML = '';

                Array.from(files).forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const col = document.createElement('div');
                        col.className = 'col-6 col-sm-4 col-md-3 col-lg-2 mb-2';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-fluid rounded shadow-sm';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';

                        col.appendChild(img);
                        previewContainer.appendChild(col);
                    };

                    reader.readAsDataURL(file);
                });
            }
        }

        document.querySelectorAll('[data-set-thumbnail]').forEach(button => {
            button.addEventListener('click', () => setThumbnail(button.dataset.setThumbnail));
        });

        document.querySelectorAll('[data-delete-image]').forEach(button => {
            button.addEventListener('click', () => deleteImage(button.dataset.deleteImage));
        });

        function setThumbnail(id) {
            fetch("{{ url('/admin/properties/' . $property->id . '/images') }}/" + id + "/set-thumbnail", {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('No se pudo cambiar la imagen principal.');
                    }

                    location.reload();
                })
                .catch(() => alert('No se pudo cambiar la imagen principal'));
        }

        function deleteImage(id) {
            if (!confirm('¿Seguro que quieres eliminar esta imagen?')) {
                return;
            }

            fetch("{{ url('/admin/properties/images') }}/" + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('No se pudo eliminar la imagen.');
                    }

                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('No se pudo eliminar la imagen');
                    }
                })
                .catch(() => alert('No se pudo eliminar la imagen'));
        }
    </script>
@endpush
