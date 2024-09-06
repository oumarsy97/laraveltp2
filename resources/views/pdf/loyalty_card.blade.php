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

        .bg-indigo-600 {
            background-color: #6366f1;
        }

        .text-indigo-600 {
            color: #6366f1;
        }

        .border-indigo-600 {
            border-color: #6366f1;
        }

        .text-white {
            color: #fff;
        }

        .bg-white {
            background-color: #fff;
        }
    </style>
</head>
<body class="bg-gray-50 flex justify-center items-center min-h-screen">

    <!-- Carte de fidélité -->
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-3xl text-center relative">

        <!-- Header -->
        <div class="bg-indigo-600 text-white py-6 rounded-t-2xl">
            <h1 class="text-4xl font-bold">Carte de Fidélité</h1>
        </div>

        <!-- Contenu principal -->
        <div class="flex flex-col items-center py-10">

            <!-- Avatar de l'utilisateur -->
            <div class="relative w-40 h-40 rounded-full border-8 border-indigo-500 shadow-lg overflow-hidden mb-6">
                <img src="{{ $user->photo ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y' }}" alt="Photo de {{ $user->nom }} {{ $user->prenom }}" class="w-full h-full object-cover">
            </div>

            <!-- Nom de l'utilisateur -->
            <h2 class="text-3xl font-semibold text-gray-700 mb-4">{{ $user->nom }} {{ $user->prenom }}</h2>


            <!-- QR Code -->
            <div class="bg-gray-100 p-6 rounded-xl shadow-md mb-6">
                <img src="{{ $qrCodePath }}" alt="QR Code" class="w-48 h-48 object-cover">
            </div>

        </div>

        <!-- Footer avec un design courbe -->
        <div class="bg-indigo-600 h-16 w-full rounded-b-2xl absolute bottom-0 left-0 flex justify-center items-center">
            <p class="text-white font-semibold">Merci pour votre fidélité</p>
        </div>
    </div>

</body>
</html>
