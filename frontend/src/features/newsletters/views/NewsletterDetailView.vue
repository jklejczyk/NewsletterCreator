<script setup lang="ts">

import StatusBadge from "@/features/newsletters/components/StatusBadge.vue";
import {Button} from "@/shared/components/ui/button";
import {useNewsletterStore} from "@/features/newsletters/store.ts";
import {useNewsletterFilters} from "@/features/newsletters/composables/useNewsletterFilters.ts";
import {onMounted} from "vue";
import {useRoute} from "vue-router";
import {formatDate} from "@/shared/utils/date.ts";

const store = useNewsletterStore()
const {filters} = useNewsletterFilters()
const route = useRoute()

function retry() {
    store.fetchList(filters.value)
}

onMounted(() => {
    store.fetchStats(Number(route.params.id))
})
</script>

<template>
    <div class="space-y-6">
        <RouterLink
            to="/admin/newsletters"
            class="inline-flex items-center text-sm text-muted-foreground hover:text-primary transition-colors"
        >
            Wróć do listy
        </RouterLink>

        <div v-if="store.isDetailLoading" class="space-y-4">
            <div class="h-8 w-1/2 bg-muted rounded animate-pulse" />
            <div class="h-4 w-1/3 bg-muted rounded animate-pulse" />
            <div class="h-32 bg-muted rounded animate-pulse" />
        </div>

        <div
            v-else-if="store.detailStatus === 'error'" class="rounded-lg border border-destructive/30 bg-destructive/5 p-6 space-y-3"
        >
            <div>
                <p class="font-medium text-destructive">Nie udało się
                    pobrać newslettera</p>
                <p class="text-sm mt-1 text-muted-foreground">{{ store.detailError }}</p>
            </div>
            <Button variant="outline" @click="retry">Spróbuj ponownie</Button>
        </div>

        <template v-else-if="store.currentNewsletter">
            <header class="space-y-3">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <h1 class="text-2xl font-bold">
                        {{ store.currentNewsletter.subject }}
                    </h1>
                    <StatusBadge :status="store.currentNewsletter.status" />
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ store.currentNewsletter.sent_at ?? 'Nie wysłany' }}
                    <span class="text-muted-foreground/50 mx-1">·</span>
                    {{ store.currentNewsletter.recipient_count }} odbiorców
                </p>
            </header>

            <section class="space-y-3">
                <h2 class="text-lg font-semibold">Wysyłki</h2>

                <p
                    v-if="!store.currentNewsletter.sends || store.currentNewsletter.sends.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    Brak zarejestrowanych wysyłek.
                </p>

                <div v-else class="rounded-lg border overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50">
                        <tr>
                            <th class="text-left px-4 py-2 font-medium">Subskrybent</th>
                            <th class="text-left px-4 py-2 font-medium">Email</th>
                            <th class="text-left px-4 py-2 font-medium">Wysłano</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <tr
                            v-for="send in store.currentNewsletter.sends"
                            :key="send.subscriber.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-4 py-2">{{ send.subscriber.name }}</td>
                            <td class="px-4 py-2 text-muted-foreground">
                                {{ send.subscriber.email }}
                            </td>
                            <td class="px-4 py-2 text-muted-foreground">
                                {{ formatDate(send.sent_at) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </template>
    </div>
</template>