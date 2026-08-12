<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    brag: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="min-h-screen bg-brag-base py-10 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-3xl mx-auto space-y-8">
            <header class="flex items-center justify-between border-b border-white/10 pb-6">
                <h1 class="text-3xl md:text-4xl font-bold font-agdasima text-brag-primary uppercase">
                    Detalhes da Conquista
                </h1>
                <Link 
                    :href="route('dashboard')" 
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-white/10 border border-white/20 rounded-lg hover:bg-white/20 transition-all focus:outline-none focus:ring-2 focus:ring-white/50"
                >
                    &larr; Voltar ao Dashboard
                </Link>
            </header>

            <article class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl space-y-8">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-4">{{ brag.title }}</h2>
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span v-for="(tech, index) in brag.technologies" :key="index" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brag-cyan/10 text-brag-cyan border border-brag-cyan/20">
                            {{ tech }}
                        </span>
                    </div>
                </div>

                <div class="space-y-6">
                    <section>
                        <h3 class="text-xl font-semibold text-brag-primary mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Contexto
                        </h3>
                        <p class="text-gray-300 leading-relaxed text-lg bg-black/20 p-4 rounded-xl border border-white/5">
                            {{ brag.context }}
                        </p>
                    </section>

                    <section>
                        <h3 class="text-xl font-semibold text-brag-primary mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Impacto & Métricas
                        </h3>
                        <p class="text-gray-300 leading-relaxed text-lg bg-black/20 p-4 rounded-xl border border-white/5">
                            {{ brag.impact }}
                        </p>
                    </section>
                </div>
                
                <div class="pt-4 border-t border-white/10 text-sm text-gray-500">
                    <p>Registrado em {{ new Date(brag.created_at).toLocaleDateString('pt-BR') }}</p>
                </div>
            </article>
        </div>
    </div>
</template>
