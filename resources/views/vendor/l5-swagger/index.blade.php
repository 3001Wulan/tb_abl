<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'API Documentation' }}</title>

    {{-- CSS --}}
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation ?? 'default', 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation ?? 'default', 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation ?? 'default', 'favicon-16x16.png') }}" sizes="16x16"/>

    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa;
            </style>
</head>

<body>
<div id="swagger-ui"></div>

{{-- JS --}}
<script src="{{ l5_swagger_asset($documentation ?? 'default', 'swagger-ui-bundle.js') }}"></script>
<script src="{{ l5_swagger_asset($documentation ?? 'default', 'swagger-ui-standalone-preset.js') }}"></script>

<script>
    window.onload = function() {
        // Ganti URL yang bermasalah dengan URL publik yang terverifikasi
        const specUrl = "http://localhost/tb_abl/public/api-docs/api-docs.yaml"; 
        
        const ui = SwaggerUIBundle({
            url: specUrl,
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
        });
        window.ui = ui;
    };
</script>
</body>
</html>
