<script setup lang="ts">
import { Button } from '@/shared/components/ui/button'
import PreferencesPicker from './PreferencesPicker.vue'
import {useSubscribeForm} from "@/features/subscribers/composables/useSubscribeForm.ts";

const {formData, status, errors, generalError, preferencesErrors, submit, reset} = useSubscribeForm()

</script>

<template>
    <form @submit.prevent="submit" class="space-y-5 p-6 border rounded-lg bg-card max-w-md">
        <div>
            <h2 class="text-2xl font-bold text-center">Zapisz się do newslettera</h2>
            <p class="text-sm text-muted-foreground mt-1 text-center">
                Codzienna dawka najciekawszych artykułów <br/>wybranych przez AI.
            </p>
        </div>

        <div v-if="status === 'success'" class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 space-y-3">
            <div>
                <p class="font-medium text-emerald-900 text-center">Rejestracja przyjęta</p>
                <p class="text-sm text-emerald-800 mt-1 text-center">
                    Sprawdź skrzynkę e-mail i kliknij link potwierdzający.
                </p>
            </div>
        </div>

        <template v-else>
            <div
                v-if="generalError"
                class="rounded-lg border border-destructive/30 bg-destructive/5 p-3 text-sm text-destructive"
            >
                {{ generalError }}
            </div>

            <div class="space-y-1.5">
                <label for="sub-email" class="text-sm font-medium">Email</label>
                <input
                    id="sub-email"
                    type="text"
                    v-model="formData.email"
                    :disabled="status === 'submitting'"
                    class="w-full h-9 px-3 rounded-md border bg-background text-sm disabled:opacity-50"
                    :class="errors.email ? 'border-destructive' : 'border-input'"
                />
                <p v-if="errors.email" class="text-sm text-destructive">
                    {{ errors.email[0] }}
                </p>
            </div>

            <div class="space-y-1.5">
                <label for="sub-name" class="text-sm font-medium">Imię</label>
                <input
                    id="sub-name"
                    type="text"
                    v-model="formData.name"
                    :disabled="status === 'submitting'"
                    class="w-full h-9 px-3 rounded-md border bg-background text-sm disabled:opacity-50"
                    :class="errors.name ? 'border-destructive' : 'border-input'"
                />
                <p v-if="errors.name" class="text-sm text-destructive">
                    {{ errors.name[0] }}
                </p>
            </div>

            <PreferencesPicker
                v-model="formData.preferences"
                :errors="preferencesErrors"
            />

            <Button type="submit" :disabled="status === 'submitting'" class="w-full">
                {{ status === 'submitting' ? 'Zapisuję...' : 'Zapisz się' }}
            </Button>
        </template>
    </form>
</template>