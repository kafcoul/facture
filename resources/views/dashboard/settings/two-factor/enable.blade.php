<x-client-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <a href="{{ route('client.settings.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour aux paramètres
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Activer l'authentification à deux facteurs</h1>
                <p class="mt-2 text-gray-600">Ajoutez une couche de sécurité supplémentaire à votre compte.</p>
            </div>

            <!-- Instructions -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Configuration</h2>
                    
                    <!-- Étape 1 -->
                    <div class="mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white font-semibold">
                                    1
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Téléchargez une application d'authentification</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Installez Google Authenticator, Authy, ou toute autre application compatible TOTP sur votre smartphone.
                                </p>
                                <div class="mt-2 flex space-x-4">
                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                        Google Play
                                    </a>
                                    <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">
                                        App Store
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 2 -->
                    <div class="mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white font-semibold">
                                    2
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Scannez le QR code</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Ouvrez votre application d'authentification et scannez le code QR ci-dessous.
                                </p>
                                
                                <!-- QR Code -->
                                <div class="mt-4 p-6 bg-gray-50 rounded-lg inline-block">
                                    {!! $qrCodeSvg !!}
                                </div>

                                <!-- Code manuel -->
                                <div class="mt-4">
                                    <p class="text-sm text-gray-700 font-medium">Ou saisissez ce code manuellement :</p>
                                    <div class="mt-2 bg-gray-100 px-4 py-3 rounded border border-gray-300">
                                        <code class="text-sm font-mono">{{ $secret }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étape 3 -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white font-semibold">
                                    3
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-medium text-gray-900">Vérifiez votre code</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Saisissez le code à 6 chiffres généré par votre application pour confirmer la configuration.
                                </p>

                                <!-- Formulaire de vérification -->
                                <form action="{{ route('client.two-factor.confirm') }}" method="POST" class="mt-4">
                                    @csrf
                                    
                                    <div class="max-w-xs">
                                        <label for="code" class="block text-sm font-medium text-gray-700">Code de vérification</label>
                                        <input 
                                            type="text" 
                                            name="code" 
                                            id="code" 
                                            maxlength="6"
                                            placeholder="000000"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                                            required
                                            autofocus
                                        >
                                        @error('code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mt-6 flex space-x-3">
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            Confirmer et activer
                                        </button>
                                        <a 
                                            href="{{ route('client.settings.index') }}" 
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            Annuler
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avertissement de sécurité -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Après avoir activé l'authentification à deux facteurs, vous recevrez des codes de récupération. Conservez-les dans un endroit sûr - ils vous permettront d'accéder à votre compte si vous perdez votre appareil.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
