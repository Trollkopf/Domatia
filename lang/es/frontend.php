<?php

return [
    'common' => [
        'image_of' => 'Imagen de :title',
        'reference_short' => 'Ref.',
        'pending' => 'Pendiente',
        'photos' => '{1} :count foto|[2,*] :count fotos',
    ],
    'contact' => ['map' => 'Mapa', 'phone_prefix' => 'Teléfono:', 'email_prefix' => 'Correo electrónico:'],
    'profile_page' => ['kicker'=>'Cuenta','title'=>'Mi perfil','intro'=>'Mantén tus datos al día, refuerza la seguridad de acceso y gestiona tu cuenta desde un solo lugar.','back_to_backoffice'=>'Volver al backoffice','back_home'=>'Volver al inicio','role'=>'Rol','email_status'=>'Estado del correo','verified'=>'Verificado','verification_pending'=>'Pendiente de verificar','member_since'=>'Miembro desde','no_date'=>'Sin fecha','manage_account'=>'Gestionar cuenta','go_to_settings'=>'Ir a ajustes','verification_sent'=>'Hemos enviado un nuevo enlace de verificación a tu correo.'],
    'environment' => [
        'title' => 'Zonas',
        'intro' => 'Descubre las zonas en las que trabajamos y conoce mejor su estilo de vida, su entorno y las propiedades disponibles en cada una.',
    ],
    'properties' => [
        'filter_title' => 'Filtrar propiedades',
        'filter_intro' => 'Ajusta la búsqueda para encontrar rápidamente las propiedades que encajan contigo.',
        'favorites_kicker' => 'Selección guardada',
        'favorites_count' => '{1} :count propiedad guardada|[2,*] :count propiedades guardadas',
        'type_in_location' => ':type en :location',
        'video' => 'Ver vídeo',
        'virtual_tour' => 'Ver visita virtual',
        'technical_sheet' => 'Ficha técnica',
        'highlighted_features' => 'Características destacadas',
        'map_location' => 'Ubicación en el mapa',
        'contact_default_message' => 'Solicitud de información sobre la propiedad :title (:reference)',
        'name_placeholder' => 'Tu nombre',
        'features' => [
            'pool' => 'Piscina', 'patio' => 'Jardín o patio', 'plot_available' => 'Parcela disponible',
            'plot_area' => ':area m² de parcela', 'air_conditioning' => 'Aire acondicionado', 'garage' => 'Garaje',
            'lift' => 'Ascensor', 'parking' => 'Aparcamiento', 'terrace' => 'Terraza', 'garden' => 'Jardín',
            'solarium' => 'Solárium', 'storage_room' => 'Trastero', 'furnished' => 'Amueblada',
            'sea_views' => 'Vistas al mar', 'new_build' => 'Obra nueva', 'featured' => 'Propiedad destacada',
        ],
        'details' => [
            'town' => 'Población', 'province' => 'Provincia', 'country' => 'País', 'location_detail' => 'Zona detallada',
            'price' => 'Precio', 'operation' => 'Operación', 'energy_consumption' => 'Consumo energético',
            'emissions' => 'Emisiones', 'reference' => 'Referencia', 'source_date' => 'Fecha de origen',
        ],
    ],
    'profile' => [
        'access_title' => 'Información de acceso', 'access_intro' => 'Actualiza el nombre visible y el correo principal asociado a la cuenta.',
        'full_name' => 'Nombre completo', 'email' => 'Correo electrónico', 'email_unverified' => 'Correo pendiente de verificación.',
        'email_verify_help' => 'Verifica la dirección para terminar de proteger tu cuenta.', 'resend_link' => 'Reenviar enlace',
        'save_changes' => 'Guardar cambios', 'profile_updated' => 'Perfil actualizado correctamente',
        'security_title' => 'Seguridad de la cuenta', 'security_intro' => 'Usa una contraseña robusta para mantener el acceso protegido.',
        'current_password' => 'Contraseña actual', 'new_password' => 'Nueva contraseña', 'confirm_password' => 'Confirmar nueva contraseña',
        'update_password' => 'Actualizar contraseña', 'password_updated' => 'Contraseña actualizada correctamente',
        'danger_title' => 'Eliminar cuenta', 'danger_intro' => 'Esta acción elimina el acceso de forma permanente. Hazlo solo si estás completamente seguro.',
        'delete_password' => 'Contraseña actual para confirmar', 'password_placeholder' => 'Introduce tu contraseña',
        'delete_warning' => 'La eliminación es irreversible y borra los datos asociados a la cuenta.', 'delete_account' => 'Eliminar cuenta',
    ],
];
