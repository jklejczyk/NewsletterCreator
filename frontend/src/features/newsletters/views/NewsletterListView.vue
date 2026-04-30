<script setup lang="ts">

import Pagination from "@/shared/components/Pagination.vue";
import NewsletterRow from "@/features/newsletters/components/NewsletterRow.vue";
import {Button} from "@/shared/components/ui/button";
import {STATUS_META, STATUS_VALUES} from "@/features/newsletters/statuses.ts";
import {useNewsletterStore} from "@/features/newsletters/store.ts";
import {ref, watch} from "vue";
import {useNewsletterFilters} from "@/features/newsletters/composables/useNewsletterFilters.ts";
import type {NewsletterStatus} from "@/features/newsletters/types.ts";

const store = useNewsletterStore()
const {filters, hasActiveFilters, patchFilters, resetFilters} = useNewsletterFilters()
const lastSendMessage = ref<string | null>(null)

watch(filters, (newFilters) => store.fetchList(newFilters), {
    immediate: true,
})

function onPageChange(page: number) {
    patchFilters({page})
}

async function onSend() {
    const message = await store.triggerSend()

    if (message) {
        lastSendMessage.value = message
        await store.fetchList(filters.value)
    }
}

function onStatusChange(event: Event) {
    const value = (event.target as HTMLSelectElement).value
    patchFilters({ status: value === '' ? undefined : (value as NewsletterStatus) },{ resetPage: true }) }

function onDateFromChange(event: Event) {
    const from = (event.target as HTMLInputElement).value
    patchFilters({ date_from: from || undefined }, { resetPage: true})
}
function onDateToChange(event: Event) {
    const to = (event.target as HTMLInputElement).value
    patchFilters({ date_to: to || undefined }, { resetPage: true})
}

function retry() {
    store.fetchList(filters.value)
}
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h1 class="text-2xl font-bold">Newslettery</h1>
            <Button @click="onSend" :disabled="store.isSending">
                {{ store.isSending ? 'Wysyłam...' : 'Wyślij newsletter' }}
            </Button>
        </div>

        <div class="flex flex-wrap gap-3 items-end p-4 border rounded-lg bg-muted/30">
            <div class="flex flex-col gap-1.5">
                <label for="filter-status" class="text-sm font-medium">Status</label>
                <select
                    id="filter-status"
                    :value="filters.status ?? ''"
                    @change="onStatusChange"
                    class="h-9 px-3 rounded-md border bg-background text-sm"
                >
                    <option value="">Wszystkie</option>
                    <option v-for="key in STATUS_VALUES" :key="key"
                            :value="key">
                        {{ STATUS_META[key].label }}
                    </option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="filter-from" class="text-sm font-medium">Od</label>
                <input
                    id="filter-from"
                    type="date"
                    :value="filters.date_from ?? ''"
                    @change="onDateFromChange"
                    class="h-9 px-3 rounded-md border bg-background text-sm"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="filter-to" class="text-sm font-medium">Do</label>
                <input
                    id="filter-to"
                    type="date"
                    :value="filters.date_to ?? ''"
                    @change="onDateToChange"
                    class="h-9 px-3 rounded-md border bg-background text-sm"
                />
            </div>

            <Button v-if="hasActiveFilters" variant="outline" @click="resetFilters">
                Resetuj filtry
            </Button>
        </div>

        <div
            v-if="lastSendMessage"
            class="rounded-lg border border-emerald-300 bg-emerald-50 p-3 text-sm text-emerald-900"
        >
            {{ lastSendMessage }}
        </div>
        <div
            v-if="store.sendError"
            class="rounded-lg border border-destructive/30 bg-destructive/5 p-3 text-sm text-destructive"
        >
            {{ store.sendError }}
        </div>

        <div v-if="store.isLoading" class="space-y-2">
            <div
                v-for="i in 4"
                :key="i"
                class="h-20 rounded-lg border bg-muted animate-pulse"
            />
        </div>

        <div
            v-else-if="store.hasError"
            class="rounded-lg border border-destructive/30 bg-destructive/5 p-6 space-y-3"
        >
            <div>
                <p class="font-medium text-destructive">Nie udało się
                    pobrać newsletterów</p>
                <p class="text-sm mt-1 text-muted-foreground">{{ store.error }}</p>
            </div>
            <Button variant="outline" @click="retry">Spróbuj
                ponownie
            </Button>
        </div>

        <div v-else-if="store.isEmpty" class="text-center py-12 space-y-3">
            <p class="text-muted-foreground">
                {{ hasActiveFilters ? 'Brak newsletterów dla wybranych filtrów.' : 'Brak newsletterów.' }}
            </p>
            <Button v-if="hasActiveFilters" variant="outline"
                    @click="resetFilters">
                Resetuj filtry
            </Button>
        </div>

        <template v-else>
            <p v-if="store.meta" class="text-sm text-muted-foreground">
                Pokazuję {{ store.newsletters.length }} z {{ store.meta.total }}
            </p>
            <div class="space-y-2">
                <NewsletterRow
                    v-for="newsletter in store.newsletters"
                    :key="newsletter.id"
                    :newsletter="newsletter"
                />
            </div>
            <Pagination
                v-if="store.meta && store.meta.last_page > 1"
                :meta="store.meta"
                @page-change="onPageChange"
            />
        </template>
    </div>
</template>