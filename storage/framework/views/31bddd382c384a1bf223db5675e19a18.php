<?php if (isset($component)) { $__componentOriginalaa758e6a82983efcbf593f765e026bd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa758e6a82983efcbf593f765e026bd9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::message'),'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('mail::message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
# Bienvenue sur <?php echo e(config('app.name')); ?> ! 🎉

Bonjour <?php echo e($userName); ?>,

Votre compte a été créé avec succès. Vous êtes inscrit(e) au plan **<?php echo e($planLabel); ?>**.

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trialEndsAt): ?>
Votre période d'essai se termine le **<?php echo e($trialEndsAt); ?>**.
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

## 🚀 Pour commencer

Voici les premières étapes pour bien démarrer :

1. **Créez vos premiers clients** — Ajoutez vos contacts professionnels
2. **Ajoutez vos produits/services** — Définissez votre catalogue
3. **Créez votre première facture** — En quelques clics !
4. **Personnalisez vos paramètres** — Logo, coordonnées, devise

<?php if (isset($component)) { $__componentOriginal15a5e11357468b3880ae1300c3be6c4f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::button'),'data' => ['url' => $dashboardUrl,'color' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('mail::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dashboardUrl),'color' => 'primary']); ?>
Accéder à mon tableau de bord
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal15a5e11357468b3880ae1300c3be6c4f)): ?>
<?php $attributes = $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f; ?>
<?php unset($__attributesOriginal15a5e11357468b3880ae1300c3be6c4f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal15a5e11357468b3880ae1300c3be6c4f)): ?>
<?php $component = $__componentOriginal15a5e11357468b3880ae1300c3be6c4f; ?>
<?php unset($__componentOriginal15a5e11357468b3880ae1300c3be6c4f); ?>
<?php endif; ?>

## 💡 Fonctionnalités disponibles

<?php $__env->startComponent('mail::table'); ?>
| Fonctionnalité | Disponible |
|:---|:---:|
| Création de factures | ✅ |
| Export PDF | ✅ |
| Envoi par email | ✅ |
| Paiements en ligne | ✅ |
| Multi-devises | <?php echo e($planLabel !== 'Starter (Gratuit)' ? '✅' : '❌'); ?> |
| Gestion d'équipe | <?php echo e(str_contains($planLabel, 'Enterprise') ? '✅' : '❌'); ?> |
| API REST | <?php echo e(str_contains($planLabel, 'Enterprise') ? '✅' : '❌'); ?> |
<?php echo $__env->renderComponent(); ?>

Besoin d'aide ? Répondez simplement à cet email.

Cordialement,<br>
L'équipe <?php echo e(config('app.name')); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf6ee7f5f2bf3e19915554c79eb482972)): ?>
<?php $attributes = $__attributesOriginalf6ee7f5f2bf3e19915554c79eb482972; ?>
<?php unset($__attributesOriginalf6ee7f5f2bf3e19915554c79eb482972); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf6ee7f5f2bf3e19915554c79eb482972)): ?>
<?php $component = $__componentOriginalf6ee7f5f2bf3e19915554c79eb482972; ?>
<?php unset($__componentOriginalf6ee7f5f2bf3e19915554c79eb482972); ?>
<?php endif; ?>
<?php /**PATH /Users/teya2023/Downloads/invoice-saas-starter/resources/views/emails/welcome.blade.php ENDPATH**/ ?>