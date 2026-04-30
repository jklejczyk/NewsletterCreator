import type { RouteRecordRaw } from 'vue-router'

export const subscriberRoutes: RouteRecordRaw[] = [
    {
        path: 'subscribe',
        name: 'subscribe',
        component: () => import('./views/SubscribeView.vue'),
    },
    {
        path: 'confirm/:token',
        name: 'confirm-subscription',
        component: () => import('./views/ConfirmView.vue'),
    },
    {
        path: 'unsubscribe/:id',
        name: 'unsubscribe',
        component: () => import('./views/UnsubscribeView.vue'),
    },
]