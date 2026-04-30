<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { confirmSubscription } from '@/features/subscribers/api'
import { ApiError, ValidationError } from '@/shared/api/errors'
import { Button } from '@/shared/components/ui/button'
import type {RequestStatus} from "@/shared/types/status.ts";

const route = useRoute()
const status = ref<RequestStatus>('loading')
const message = ref<string>('')
const errorMessage = ref<string>('')

onMounted(async () => {
    const token = route.params.token
    if (typeof token !== 'string' || token.length === 0) {
        status.value = 'error'
        errorMessage.value = 'Niepoprawny link aktywacyjny.'
        return
    }

    try {
        const response = await confirmSubscription(token)
        status.value = 'success'
        message.value = response.message
    } catch (err) {
        status.value = 'error'
        if (err instanceof ApiError || err instanceof ValidationError) {
            console.log(err)
            errorMessage.value = err.message
        } else {
            errorMessage.value = 'Wystąpił nieoczekiwany błąd.'
        }
    }
})
</script>

<template>
    <div class="max-w-md mx-auto py-16 text-center space-y-4">
        <template v-if="status === 'loading'">
            <h1 class="text-2xl font-bold">Potwierdzanie...</h1>
            <p class="text-muted-foreground">Weryfikacja, proszę chwilę zaczekać.</p>
        </template>

        <template v-else-if="status === 'success'">
            <h1 class="text-2xl font-bold text-emerald-700">Email potwierdzony</h1>
            <p class="text-muted-foreground">{{ message }}</p>
            <Button as-child>
                <RouterLink to="/">Wróć do artykułów</RouterLink>
            </Button>
        </template>

        <template v-else>
            <h1 class="text-2xl font-bold text-destructive">Nie udało się potwierdzić</h1>
            <p class="text-muted-foreground">{{ errorMessage }}</p>
            <Button as-child variant="outline">
                <RouterLink to="/subscribe">Spróbuj zarejestrować się ponownie</RouterLink>
            </Button>
        </template>
    </div>
</template>