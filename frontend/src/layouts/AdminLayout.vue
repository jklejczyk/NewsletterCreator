<script setup lang="ts">
import {RouterLink, RouterView} from 'vue-router'
</script>

<template>
    <div class="min-h-screen bg-background flex">
        <aside class="w-64 border-r bg-muted/30 flex flex-col">
            <div class="p-4 border-b">
                <h2 class="font-bold">Admin Panel</h2>
                <p class="text-xs text-muted-foreground mt-1">Newsletter Creator</p>
            </div>
            <nav class="p-4 space-y-1 flex flex-col text-sm">
<!--                <RouterLink-->
<!--                    to="/admin"-->
<!--                    class="px-3 py-2 rounded-md hover:bg-muted transition-colors"-->
<!--                    active-class="bg-muted font-medium"-->
<!--                >-->
<!--                    Dashboard-->
<!--                </RouterLink>-->
                <RouterLink
                    to="/admin/newsletters"
                    class="px-3 py-2 rounded-md hover:bg-muted transition-colors"
                    active-class="bg-muted font-medium"
                >
                    Newslettery
                </RouterLink>
            </nav>
        </aside>

        <main class="flex-1 p-8 overflow-auto">
            <RouterView/>
        </main>
    </div>
</template>