<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { unsubscribe } from '@/features/subscribers/api'
import { ApiError } from '@/shared/api/errors'
import { Button } from '@/shared/components/ui/button'
import type {RequestStatus} from "@/shared/types/status.ts";

const route = useRoute()
const status = ref<RequestStatus>('loading')
const message = ref<string>('')
const errorMessage = ref<string>('')

onMounted(async () => {
    const idParam = route.params.id
    const id = typeof idParam === 'string' ? Number(idParam) : NaN

    if (!Number.isFinite(id) || id <= 0) {
        status.value = 'error'
        errorMessage.value = 'Niepoprawny link wypisu.'
        return
    }

    try {
        const response = await unsubscribe(id)
        status.value = 'success'
        message.value = response.message
    } catch (err) {
        status.value = 'error'
        errorMessage.value = err instanceof ApiError ? err.message : 'Wystąpił błąd.'
    }
})
</script>

<template>
    <div class="max-w-md mx-auto py-16 text-center space-y-4">
        <template v-if="status === 'loading'">
            <h1 class="text-2xl font-bold">Wypisywanie...</h1>
            <p class="text-muted-foreground">Anulowanie subskrypcji w toku.</p>
        </template>

        <template v-else-if="status === 'success'">
            <h1 class="text-2xl font-bold">Wypisano z newslettera</h1>
            <p class="text-muted-foreground">{{ message }}</p>
            <Button as-child>
                <RouterLink to="/">Wróć do strony głównej</RouterLink>
            </Button>
        </template>

        <template v-else>
            <h1 class="text-2xl font-bold text-destructive">Nie udało się wypisać</h1>
            <p class="text-muted-foreground">{{ errorMessage }}</p>
        </template>
    </div>
</template>