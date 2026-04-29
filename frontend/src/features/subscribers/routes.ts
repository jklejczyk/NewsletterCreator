import type { RouteRecordRaw } from 'vue-router'

export const subscriberRoutes: RouteRecordRaw[] = [
    {
        path: 'subscribe',
        name: 'subscribe',
        component: () => import('./views/SubscribeView.vue'),
    },
]