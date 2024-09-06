<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de fidélité</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-lg relative text-center">
        <!-- Pattern Top -->
        <div class="w-full h-32 bg-cover bg-center rounded-t-xl" style="background-image: url('./assets/pattern-waves.svg');"></div>

        <!-- Avatar -->
        <div class="relative -mt-16">
            <div class="w-36 h-36 rounded-full mx-auto border-4 border-white shadow-lg overflow-hidden">
                <img src="{{ $user->photo ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y' }}" alt="Profile photo of {{ $user->nom }} {{ $user->prenom }}" class="w-full h-full object-cover">
            </div>
        </div>

        <!-- Titre -->
        <h1 class="text-2xl font-bold text-gray-700 mt-6">Carte De Fidélité</h1>

        <!-- Nom de l'utilisateur -->
        <p class="text-xl font-semibold text-gray-600 mt-2">{{ $user->nom }} {{ $user->prenom }}</p>

        <!-- QR Code -->
        <div class="mt-8 bg-white p-4 rounded-lg shadow-lg inline-block">
            <img src="{{ $qrCodePath }}" alt="QR Code" class="w-40 h-40 object-cover">
        </div>

        <!-- Pattern Bottom -->
        <div class="w-full h-32 bg-cover bg-center rounded-b-xl absolute bottom-0 left-0" style="background-image: url('./assets/flat-geometric-background.svg');"></div>
    </div>
</body>
</html>
