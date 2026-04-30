<script setup lang="ts">
import {STATUS_META} from "@/features/newsletters/statuses.ts";
import type {NewsletterStatus} from "@/features/newsletters/types.ts";

defineProps<{
    status: NewsletterStatus
}>()
</script>

<template>
    <span
        class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
        :class="STATUS_META[status].colorClasses"
    >
      {{ STATUS_META[status].label }}
    </span>
</template>

