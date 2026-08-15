<script setup>
import { useForm, Link, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import ErrorModal from '@/Components/ErrorModal.vue';

const page = usePage();

const props = defineProps({
    brags: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    raw_brag: '',
});

const showErrorModal = ref(false);
const errorMessage = ref('');

const submit = () => {
    form.post('/brag/generate', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('raw_brag');
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0] || 'Erro ao processar a conquista.';
            errorMessage.value = firstError;
            showErrorModal.value = true;
        },
    });
};

watch(() => page.props.flash?.success, (successMessage) => {
    if (successMessage) {
        toast.success(successMessage, { position: 'top-center' });
    }
});

watch(() => page.props.flash?.error, (flashError) => {
    if (flashError) {
        errorMessage.value = flashError;
        showErrorModal.value = true;
    }
});

onMounted(() => {
    if (page.props.flash?.success) {
        toast.success(page.props.flash.success, { position: 'top-center' });
    }
    if (page.props.flash?.error) {
        errorMessage.value = page.props.flash.error;
        showErrorModal.value = true;
    }
});
</script>

<template>
    <Toaster richColors position="top-center" />

    <ErrorModal 
        :show="showErrorModal" 
        title="Erro ao Destilar Conquista" 
        :message="errorMessage" 
        @close="showErrorModal = false" 
    />

    <div class="min-h-screen bg-brag-base py-10 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-4xl mx-auto space-y-8">
            <header class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold font-agdasima text-brag-primary tracking-wide uppercase">
                    Brag-Bot | Pós IA UNIPDS
                </h1>
                <p class="mt-2 text-lg text-gray-300 font-sans font-light">
                    Destile e documente suas conquistas de forma inteligente com Google Genkit.
                </p>
            </header>

            <section class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 md:p-8 shadow-xl">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="raw_brag" class="block text-sm font-medium text-gray-300 mb-2">Descreva sua conquista bruta</label>
                        <textarea 
                            id="raw_brag" 
                            v-model="form.raw_brag" 
                            rows="4" 
                            class="w-full bg-black/20 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brag-primary focus:border-transparent transition-all font-sans resize-y"
                            placeholder="Ex: Consegui melhorar a performance do carregamento da página inicial em 50% refatorando as queries SQL do painel de administração."
                            required
                        ></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="relative inline-flex items-center justify-center px-8 py-3 text-base font-medium text-brag-base bg-brag-primary rounded-xl overflow-hidden transition-all hover:bg-brag-primary/90 focus:outline-none focus:ring-2 focus:ring-brag-primary focus:ring-offset-2 focus:ring-offset-brag-base disabled:opacity-70 disabled:cursor-not-allowed cursor-pointer"
                        >
                            <span v-if="form.processing" class="absolute inset-0 flex items-center justify-center bg-brag-primary">
                                <svg class="animate-spin h-5 w-5 text-brag-base" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span :class="{'opacity-0': form.processing}">Destilar Conquista</span>
                        </button>
                    </div>
                </form>
            </section>

            <section class="space-y-6 pt-4">
                <h2 class="text-2xl font-semibold text-white font-agdasima">Conquistas Recentes</h2>
                
                <div v-if="brags.length === 0" class="text-center py-12 border border-dashed border-white/20 rounded-xl bg-white/5">
                    <p class="text-gray-400">Nenhuma conquista destilada ainda. O que você realizou hoje?</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Link 
                        v-for="brag in brags" 
                        :key="brag.id" 
                        :href="route('brag.show', brag.id)"
                        class="block group bg-white/5 border border-white/10 rounded-xl p-6 hover:bg-white/10 hover:border-brag-primary/50 transition-all cursor-pointer shadow-lg"
                    >
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-brag-primary transition-colors line-clamp-2">
                            {{ brag.title }}
                        </h3>
                        <p class="text-sm text-gray-400 line-clamp-3 mb-4">
                            {{ brag.context }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(tech, index) in brag.technologies" :key="index" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brag-cyan/10 text-brag-cyan border border-brag-cyan/20">
                                {{ tech }}
                            </span>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </div>
</template>
