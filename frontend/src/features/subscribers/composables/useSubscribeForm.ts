import { computed, reactive, ref } from 'vue'
import { subscribe } from '../api'
import { ApiError, ValidationError, type ValidationErrors } from '@/shared/api/errors'
import type { SubscribePayload } from '../types'

type Status = 'idle' | 'submitting' | 'success'

function emptyForm(): SubscribePayload {
    return { email: '', name: '', preferences: [] }
}

export function useSubscribeForm() {
    const formData = reactive<SubscribePayload>(emptyForm())
    const status = ref<Status>('idle')
    const errors = ref<ValidationErrors>({})
    const generalError = ref<string | null>(null)

    const preferencesErrors = computed<string[]>(() => {
        const list: string[] = []

        for (const [key, messages] of Object.entries(errors.value))
        {
            if (key === 'preferences' ||
                key.startsWith('preferences.')) {
                list.push(...messages)
            }
        }

        return list
    })

    async function submit() {
        status.value = 'submitting'
        errors.value = {}
        generalError.value = null

        try {
            await subscribe(formData)
            status.value = 'success'
        } catch (err) {
            status.value = 'idle'
            if (err instanceof ValidationError) {
                errors.value = err.errors
            } else if (err instanceof ApiError) {
                generalError.value = err.message
            } else {
                generalError.value = 'Wystąpił nieoczekiwany błąd.'
            }
        }
    }

    function reset() {
        status.value = 'idle'
        errors.value = {}
        generalError.value = null
        Object.assign(formData, emptyForm())
    }

    return {
        formData,
        status,
        errors,
        generalError,
        preferencesErrors,
        submit,
        reset,
    }
}