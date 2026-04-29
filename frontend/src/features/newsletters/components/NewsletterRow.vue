<script setup lang="ts">

import StatusBadge from "@/features/newsletters/components/StatusBadge.vue";
import type {Newsletter} from "@/features/newsletters/types.ts";

defineProps<{
    newsletter: Newsletter
}>()
</script>

<template>
    <RouterLink
        :to="{ name: 'newsletter-detail', params: { id: newsletter.id } }"
        class="flex items-center justify-between gap-4 p-4 border rounded-lg hover:bg-muted/30 transition-colors group"
    >
        <div class="space-y-1 min-w-0 flex-1">
            <h3 class="font-medium truncate group-hover:text-primary">
                {{ newsletter.subject }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ newsletter.sent_at ?? 'Nie wysłany' }}
                <span class="text-muted-foreground/50 mx-1">·</span>
                {{ newsletter.recipient_count }} odbiorców
            </p>
        </div>
        <StatusBadge :status="newsletter.status" />
    </RouterLink>
</template>