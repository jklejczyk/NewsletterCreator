import {createRouter, createWebHistory, type RouteRecordRaw} from 'vue-router'
import PublicLayout from '@/layouts/PublicLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import {articleRoutes} from "@/features/articles/routes.ts";
import {subscriberRoutes} from "@/features/subscribers/routes.ts";

const routes: RouteRecordRaw[] = [
    {
        path: '/',
        component: PublicLayout,
        children: [
            ...articleRoutes,
            ...subscriberRoutes
        ],
    },
    {
        path: '/admin',
        component: AdminLayout,
        children: [
            {
                path: '',
                name: 'admin-dashboard',
                component: {
                    template: '<div class="text-2xl" >Admin layout works</div>'
                },
            },
        ],
    },
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

export default router