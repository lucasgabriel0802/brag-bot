import { ref } from 'vue';

export const loading = ref(false);
export const error = ref(null);
export const brags = ref([]);

/**
 * Envia uma definição informal de conquista para a API do Genkit e persiste no banco.
 *
 * @param {string} definition
 * @returns {Promise<Object>}
 */
export async function generateBrag(definition) {
    if (!definition || !definition.trim()) {
        throw new Error('A descrição da conquista é obrigatória.');
    }

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch('/api/brag', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ definition: definition.trim() }),
        });

        const data = await response.json();

        if (!response.ok) {
            const errorMessage = data.message || data.error || 'Erro ao gerar conquista com IA.';
            throw new Error(errorMessage);
        }

        const newBrag = data.data || data;
        brags.value.unshift(newBrag);

        return newBrag;
    } catch (err) {
        error.value = err.message || 'Falha na comunicação com o servidor.';
        throw err;
    } finally {
        loading.value = false;
    }
}

/**
 * Busca a lista de conquistas da API.
 *
 * @returns {Promise<Array>}
 */
export async function fetchBrags() {
    loading.value = true;
    error.value = null;

    try {
        const response = await fetch('/api/v1/brags', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao carregar lista de conquistas.');
        }

        brags.value = data.data || [];
        return brags.value;
    } catch (err) {
        error.value = err.message || 'Falha ao buscar conquistas.';
        throw err;
    } finally {
        loading.value = false;
    }
}

export default {
    loading,
    error,
    brags,
    generateBrag,
    fetchBrags,
};
