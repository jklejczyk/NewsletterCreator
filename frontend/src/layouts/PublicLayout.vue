<script setup lang="ts">
import {RouterLink, RouterView} from 'vue-router'

</script>

<template>
    <div class="min-h-screen bg-background flex flex-col">
        <header class="border-b">
            <div class="container mx-auto px-4 py-4 flex items-center justify-between">
                <RouterLink to="/" class="text-xl font-bold">
                    Newsletter
                </RouterLink>
                <nav class="flex gap-6 text-sm">
                    <RouterLink
                        to="/"
                        class="hover:text-primary transition-colors"
                        active-class="text-primary font-medium"
                    >
                        Artykuły
                    </RouterLink>
                    <RouterLink
                        to="/subscribe"
                        class="hover:text-primary transition-colors"
                        active-class="text-primary font-medium"
                    >
                        Zapisz się
                    </RouterLink>
                </nav>
            </div>
        </header>

        <main class="flex-1 container mx-auto px-4 py-8">
            <RouterView/>
        </main>

        <footer class="border-t py-6">
            <div class="container mx-auto px-4 text-sm text-center text-muted-foreground">
                © 2026 Newsletter creator
            </div>
        </footer>
    </div>
</template>