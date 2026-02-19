<x-client-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête avec succès -->
            @if(isset($regenerated) && $regenerated)
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Codes de récupération régénérés avec succès !</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">L'authentification à deux facteurs a été activée avec succès !</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Titre -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Codes de récupération</h1>
                <p class="mt-2 text-gray-600">Conservez ces codes dans un endroit sûr. Ils vous permettront d'accéder à votre compte si vous perdez votre appareil d'authentification.</p>
            </div>

            <!-- Codes de récupération -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Vos codes de récupération</h2>
                            <button 
                                onclick="downloadCodes()" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Télécharger
                            </button>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200" id="recovery-codes">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($recoveryCodes as $code)
                                    <div class="bg-white px-4 py-3 rounded border border-gray-300 text-center">
                                        <code class="text-base font-mono font-semibold text-gray-900">{{ $code }}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button 
                            onclick="copyCodes()" 
                            class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Copier tous les codes
                        </button>
                        <span id="copy-feedback" class="ml-2 text-sm text-green-600 hidden">✓ Codes copiés !</span>
                    </div>
                </div>
            </div>

            <!-- Avertissement de sécurité -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Gardez ces codes en sécurité</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Chaque code ne peut être utilisé qu'une seule fois</li>
                                <li>Conservez-les dans un endroit sûr (gestionnaire de mots de passe, coffre-fort, etc.)</li>
                                <li>Ne partagez jamais ces codes avec qui que ce soit</li>
                                <li>Vous pouvez régénérer ces codes à tout moment depuis les paramètres</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Régénérer les codes -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Régénérer les codes</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Si vous pensez que vos codes ont été compromis ou si vous les avez tous utilisés, vous pouvez générer une nouvelle série de codes.
                    </p>
                    
                    <button 
                        onclick="showRegenerateModal()" 
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Régénérer les codes
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-center">
                <a 
                    href="{{ route('client.settings.index') }}" 
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                    Retour aux paramètres
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation pour régénération -->
    <div id="regenerate-modal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="{{ route('client.two-factor.recovery-codes.regenerate') }}" method="POST">
                    @csrf
                    
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Régénérer les codes de récupération
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Cela invalidera tous vos codes actuels et en générera de nouveaux. Confirmez votre mot de passe pour continuer.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                            required
                        >
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button 
                            type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm"
                        >
                            Confirmer
                        </button>
                        <button 
                            type="button" 
                            onclick="hideRegenerateModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                        >
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const codes = @json($recoveryCodes);

        function copyCodes() {
            const codesText = codes.join('\n');
            navigator.clipboard.writeText(codesText).then(() => {
                const feedback = document.getElementById('copy-feedback');
                feedback.classList.remove('hidden');
                setTimeout(() => {
                    feedback.classList.add('hidden');
                }, 2000);
            });
        }

        function downloadCodes() {
            const codesText = 'Codes de récupération - {{ config("app.name") }}\n\n' + codes.join('\n') + '\n\nConservez ces codes en lieu sûr.';
            const blob = new Blob([codesText], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'recovery-codes-{{ now()->format("Y-m-d") }}.txt';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }

        function showRegenerateModal() {
            document.getElementById('regenerate-modal').classList.remove('hidden');
        }

        function hideRegenerateModal() {
            document.getElementById('regenerate-modal').classList.add('hidden');
        }
    </script>
</x-client-layout>
