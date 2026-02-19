<?php $__env->startSection('title', 'Connexion'); ?>

<?php $__env->startSection('content'); ?>
    <div class="relative min-h-[calc(100vh-72px)] overflow-hidden">

        
        <div class="absolute inset-0 auth-mesh"></div>
        <div class="auth-aurora"></div>
        <div class="absolute inset-0 auth-dots opacity-30"></div>
        <div class="absolute inset-0 auth-grid opacity-40"></div>

        
        <div class="auth-blob auth-blob-1"></div>
        <div class="auth-blob auth-blob-2"></div>
        <div class="auth-blob auth-blob-3"></div>

        
        <div id="authParticles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

        
        <div class="relative flex min-h-[calc(100vh-72px)]">

            
            <div class="hidden lg:flex lg:w-[48%] xl:w-[45%] auth-panel-left flex-col justify-between p-10 xl:p-14">

                
                <div class="panel-shape panel-shape-1" style="--r:15deg"></div>
                <div class="panel-shape panel-shape-2" style="--r:-20deg"></div>
                <div class="panel-shape panel-shape-3" style="--r:45deg"></div>
                <div class="panel-shape panel-shape-4" style="--r:-10deg"></div>

                
                <div class="panel-line panel-line-1"></div>
                <div class="panel-line panel-line-2"></div>
                <div class="panel-line panel-line-3"></div>

                
                <div class="auth-ring" style="width:200px;height:200px;top:15%;right:10%;animation-delay:0s"></div>
                <div class="auth-ring" style="width:150px;height:150px;bottom:20%;left:8%;animation-delay:3s"></div>

                
                <div id="panelParticles" class="absolute inset-0 pointer-events-none"></div>

                
                <div class="relative z-10 auth-reveal" style="transition-delay: 200ms">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/10">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <span class="text-white/90 text-lg font-bold tracking-tight">InvoiceSaaS</span>
                    </div>
                </div>

                
                <div class="relative z-10 panel-stagger flex-1 flex flex-col justify-center -mt-8">

                    
                    <div class="auth-reveal mb-10" style="transition-delay: 400ms">
                        <div class="relative w-full max-w-[320px] mx-auto">
                            
                            <div class="auth-float-badge bg-white/[.07] backdrop-blur-sm rounded-2xl border border-white/[.08] p-6 shadow-2xl shadow-black/20"
                                style="animation-delay: 0s">
                                <div class="flex items-center justify-between mb-5">
                                    <div>
                                        <div class="h-2 w-20 bg-white/20 rounded-full mb-2"></div>
                                        <div class="h-1.5 w-14 bg-white/10 rounded-full"></div>
                                    </div>
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-brand-400/30 to-accent-400/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <div class="h-1.5 w-24 bg-white/10 rounded-full"></div>
                                        <div class="h-1.5 w-12 bg-white/10 rounded-full"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="h-1.5 w-20 bg-white/10 rounded-full"></div>
                                        <div class="h-1.5 w-16 bg-white/10 rounded-full"></div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <div class="h-1.5 w-28 bg-white/10 rounded-full"></div>
                                        <div class="h-1.5 w-10 bg-white/10 rounded-full"></div>
                                    </div>
                                    <div class="border-t border-white/[.06] pt-3 mt-3 flex justify-between items-center">
                                        <div class="h-2 w-12 bg-white/15 rounded-full"></div>
                                        <div class="h-2 w-20 bg-accent-400/30 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="auth-float-badge absolute -top-4 -right-4 bg-accent-500/90 backdrop-blur-sm text-white text-[11px] font-bold px-3 py-1.5 rounded-full shadow-lg shadow-accent-500/30 flex items-center gap-1.5"
                                style="animation-delay: 1s">
                                <svg class="w-3.5 h-3.5 auth-check-anim" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Payée
                            </div>

                            
                            <div class="auth-float-badge absolute -bottom-3 -left-4 bg-white/[.09] backdrop-blur-sm text-white/90 text-[11px] font-semibold px-3 py-1.5 rounded-full border border-white/[.08] shadow-lg flex items-center gap-1.5"
                                style="animation-delay: 2s">
                                <span class="w-2 h-2 rounded-full bg-brand-400 auth-pulse-dot"></span>
                                +245 000 FCFA
                            </div>
                        </div>
                    </div>

                    
                    <div class="auth-reveal text-center" style="transition-delay: 550ms">
                        <h2 class="text-2xl xl:text-3xl font-extrabold text-white leading-tight">
                            Gérez vos factures<br>
                            <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-purple-300 to-accent-300">en
                                toute simplicité</span>
                        </h2>
                        <p class="mt-3 text-sm text-white/50 max-w-xs mx-auto leading-relaxed">
                            La plateforme de facturation pensée pour les entrepreneurs africains.
                        </p>
                    </div>

                    
                    <div class="auth-reveal mt-8 flex justify-center gap-6 xl:gap-8" style="transition-delay: 700ms">
                        <div class="text-center stat-counter" style="animation-delay: .8s">
                            <p class="text-2xl xl:text-3xl font-extrabold text-white" data-count="2500" data-suffix="+">0
                            </p>
                            <p class="text-[11px] text-white/40 mt-0.5">Factures/mois</p>
                        </div>
                        <div class="w-px h-10 bg-white/10"></div>
                        <div class="text-center stat-counter" style="animation-delay: 1s">
                            <p class="text-2xl xl:text-3xl font-extrabold text-white" data-count="500" data-suffix="+">0
                            </p>
                            <p class="text-[11px] text-white/40 mt-0.5">Entreprises</p>
                        </div>
                        <div class="w-px h-10 bg-white/10"></div>
                        <div class="text-center stat-counter" style="animation-delay: 1.2s">
                            <p class="text-2xl xl:text-3xl font-extrabold text-white" data-count="12" data-suffix="">0
                            </p>
                            <p class="text-[11px] text-white/40 mt-0.5">Pays actifs</p>
                        </div>
                    </div>
                </div>

                
                <div class="relative z-10 auth-reveal" style="transition-delay: 900ms">
                    <div class="bg-white/[.06] backdrop-blur-sm rounded-2xl border border-white/[.08] p-5">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex-shrink-0 w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-[11px] font-bold text-white shadow-lg shadow-amber-500/20">
                                AD
                            </div>
                            <div>
                                <p class="text-white/70 text-sm leading-relaxed italic">"InvoiceSaaS a transformé ma
                                    gestion. Je gagne 5h par semaine !"</p>
                                <p class="text-white/40 text-xs mt-2 font-medium">Aminata Diallo · <span
                                        class="text-accent-400/80">CEO, TechSénégal</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="flex-1 flex items-center justify-center py-10 px-5 sm:px-8 lg:px-12 xl:px-16">
                <div class="w-full max-w-[460px]">

                    
                    <div class="auth-reveal text-center lg:text-left mb-8">
                        
                        <div
                            class="lg:hidden auth-icon-pulse inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 mb-5 shadow-lg shadow-brand-500/20">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>

                        
                        <div class="flex items-center gap-2 justify-center lg:justify-start mb-2">
                            <span
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-accent-600 bg-accent-50 px-3 py-1 rounded-full border border-accent-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-accent-500 auth-pulse-dot"></span>
                                Sécurisé & rapide
                            </span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold auth-gradient-text">Bon retour !</h2>
                        <p class="mt-2 text-slate-500 text-base">Connectez-vous à votre espace InvoiceSaaS</p>
                    </div>

                    
                    <div class="auth-reveal-scale auth-card-glow bg-white/90 backdrop-blur-xl rounded-3xl shadow-elevated border border-white/60 p-8 sm:p-10"
                        style="transition-delay: 120ms">

                        <form method="POST" action="<?php echo e(route('login.submit')); ?>" class="auth-stagger space-y-5">
                            <?php echo csrf_field(); ?>

                            
                            <div class="auth-reveal" style="transition-delay: 200ms">
                                <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Adresse email
                                </label>
                                <div class="auth-input-wrap relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </div>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        value="<?php echo e(old('email')); ?>"
                                        data-typing="vous@exemple.com|contact@monentreprise.sn|aminata@techsenegal.com"
                                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'block w-full pl-10 pr-4 py-3.5 bg-surface/80 border rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all',
                                            'border-red-400 ring-2 ring-red-100' => $errors->has('email'),
                                            'border-slate-200' => !$errors->has('email'),
                                        ]); ?>" placeholder="vous@exemple.com">
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="auth-reveal" style="transition-delay: 280ms">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="password" class="block text-xs font-semibold text-slate-600">
                                        Mot de passe
                                    </label>
                                    <a href="<?php echo e(route('password.request')); ?>"
                                        class="text-xs font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                                        Mot de passe oublié ?
                                    </a>
                                </div>
                                <div class="auth-input-wrap relative" x-data="{ show: false }">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="auth-icon h-4 w-4 text-slate-400" fill="none"
                                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>
                                    <input id="password" name="password" :type="show ? 'text' : 'password'"
                                        autocomplete="current-password" required class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'block w-full pl-10 pr-12 py-3.5 bg-surface/80 border rounded-2xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 focus:bg-white hover:border-slate-300 transition-all',
                                            'border-red-400 ring-2 ring-red-100' => $errors->has('password'),
                                            'border-slate-200' => !$errors->has('password'),
                                        ]); ?>"
                                        placeholder="••••••••">
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-600 transition-colors cursor-pointer">
                                        <svg x-show="!show" class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg x-show="show" x-cloak class="h-4 w-4" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        <?php echo e($message); ?>

                                    </p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div class="auth-reveal flex items-center" style="transition-delay: 360ms">
                                <input id="remember" name="remember" type="checkbox"
                                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded cursor-pointer">
                                <label for="remember" class="ml-2 block text-sm text-slate-600 cursor-pointer">
                                    Se souvenir de moi
                                </label>
                            </div>

                            
                            <div class="auth-reveal" style="transition-delay: 440ms">
                                <button type="submit" id="loginSubmitBtn"
                                    class="btn-primary btn-shine auth-shimmer w-full flex justify-center items-center gap-2 py-4 px-4 rounded-2xl text-sm font-bold text-white bg-gradient-to-r from-brand-600 via-brand-600 to-deep-700 hover:from-brand-700 hover:via-brand-700 hover:to-deep-800 shadow-btn cursor-pointer group relative overflow-hidden"
                                    onclick="this.querySelector('.login-btn-text')?.classList.add('hidden'); this.querySelector('.login-spinner')?.classList.remove('hidden'); this.disabled = true; this.closest('form').submit();">
                                    <span class="login-btn-text flex items-center gap-2">
                                        Se connecter
                                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                            stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <span class="login-spinner hidden"><span class="auth-spinner"></span></span>
                                </button>
                            </div>
                        </form>

                        
                        <div class="auth-reveal mt-7" style="transition-delay: 520ms">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-slate-200/80"></div>
                                </div>
                                <div class="relative flex justify-center text-xs">
                                    <span class="px-4 bg-white/90 text-slate-400 font-medium">Pas encore de compte ?</span>
                                </div>
                            </div>

                            
                            <div class="mt-5">
                                <a href="<?php echo e(route('register')); ?>"
                                    class="w-full flex justify-center items-center gap-2 py-3.5 px-4 border-2 border-slate-200/80 rounded-2xl text-sm font-semibold text-slate-700 bg-white/80 backdrop-blur-sm hover:bg-brand-50 hover:border-brand-200 hover:text-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all cursor-pointer group">
                                    <svg class="w-4 h-4 text-brand-500 group-hover:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                    </svg>
                                    Créer un compte gratuitement
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-brand-500 group-hover:translate-x-1 transition-all"
                                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    
                    <div class="auth-reveal mt-8" style="transition-delay: 600ms">
                        <div class="flex items-center justify-center lg:justify-start gap-3 mb-4">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 border-2 border-white flex items-center justify-center text-[9px] font-bold text-white shadow-md">
                                    AK</div>
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-accent-400 to-accent-600 border-2 border-white flex items-center justify-center text-[9px] font-bold text-white shadow-md">
                                    OD</div>
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 border-2 border-white flex items-center justify-center text-[9px] font-bold text-white shadow-md">
                                    FT</div>
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-rose-400 to-rose-600 border-2 border-white flex items-center justify-center text-[9px] font-bold text-white shadow-md">
                                    MK</div>
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 border-2 border-white flex items-center justify-center text-[9px] font-bold text-white shadow-md">
                                    SN</div>
                            </div>
                            <p class="text-xs text-slate-400"><span class="font-semibold text-slate-600">500+</span>
                                entreprises nous font confiance</p>
                        </div>
                    </div>

                    
                    <div class="auth-reveal mt-3" style="transition-delay: 680ms">
                        <div
                            class="flex flex-wrap items-center justify-center lg:justify-start gap-3 text-[11px] text-slate-400">
                            <span
                                class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm"
                                style="animation-delay: 0s">
                                <svg class="w-3.5 h-3.5 text-accent-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                                SSL 256-bit
                            </span>
                            <span
                                class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm"
                                style="animation-delay: .5s">
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                Données protégées
                            </span>
                            <span
                                class="auth-float-badge flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/70 backdrop-blur-sm border border-slate-100 shadow-sm"
                                style="animation-delay: 1s">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                14 jours d'essai
                            </span>
                        </div>
                    </div>

                    
                    <div class="auth-reveal mt-5" style="transition-delay: 760ms">
                        <p class="text-center lg:text-left text-xs text-slate-400">
                            En vous connectant, vous acceptez nos
                            <a href="/conditions-generales"
                                class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Conditions
                                d'utilisation</a>
                            et notre
                            <a href="/politique-confidentialite"
                                class="font-semibold text-brand-600 hover:text-brand-700 transition-colors">Politique de
                                confidentialité</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/teya2023/Downloads/invoice-saas-starter/resources/views/auth/login.blade.php ENDPATH**/ ?>