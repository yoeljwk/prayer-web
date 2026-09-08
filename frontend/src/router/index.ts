import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import HomeView from '@/views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/ruang-doa',
      name: 'prayer-rooms',
      component: () => import('@/views/PrayerRoomsView.vue'),
    },
    {
      path: '/ruang-doa/create',
      name: 'prayer-rooms-create',
      component: () => import('@/views/PrayerRoomsView.vue'),
    },
    {
      path: '/ruang-doa/:code',
      name: 'prayer-room-detail',
      component: () => import('@/views/RoomDetailView.vue'),
    },
    {
      path: '/rooms',
      redirect: '/ruang-doa',
    },
    {
      path: '/rooms/create',
      redirect: '/ruang-doa/create',
    },
    {
      path: '/rooms/:code',
      redirect: (to) => `/ruang-doa/${to.params.code}`,
    },
    {
      path: '/permohonan-doa',
      name: 'prayer-requests',
      component: () => import('@/views/PrayerRequestsView.vue'),
    },
    {
      path: '/permohonan-doa/create',
      name: 'prayer-requests-create',
      component: () => import('@/views/PrayerRequestsView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/permohonan-doa/:id',
      name: 'prayer-requests-detail',
      component: () => import('@/views/PrayerRequestsView.vue'),
    },
    {
      path: '/prayers',
      redirect: '/permohonan-doa',
    },
    {
      path: '/prayers/create',
      redirect: '/permohonan-doa/create',
    },
    {
      path: '/prayers/:id',
      redirect: (to) => `/permohonan-doa/${to.params.id}`,
    },
    {
      path: '/komunitas',
      name: 'community',
      component: () => import('@/views/CommunityView.vue'),
    },
    {
      path: '/komunitas/create',
      name: 'community-create',
      component: () => import('@/views/CommunityView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/komunitas/:slug',
      name: 'community-detail',
      component: () => import('@/views/CommunityDetailView.vue'),
    },
    {
      path: '/communities',
      redirect: '/komunitas',
    },
    {
      path: '/communities/create',
      redirect: '/komunitas/create',
    },
    {
      path: '/communities/:slug',
      redirect: (to) => `/komunitas/${to.params.slug}`,
    },
    {
      path: '/tentang',
      name: 'about',
      component: () => import('@/views/AboutView.vue'),
    },
    {
      path: '/masuk',
      name: 'login',
      alias: '/login',
      component: () => import('@/views/LoginView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/RegisterView.vue'),
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/notifications',
      name: 'notifications',
      component: () => import('@/views/NotificationsView.vue'),
      meta: { requiresAuth: true },
    },
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    const isPrayerRoute = (path: string) => path.startsWith('/prayers') || path.startsWith('/permohonan-doa')
    if (isPrayerRoute(to.path) && isPrayerRoute(from.path)) {
      return false
    }
    return { top: 0 }
  },
})

// Centralized navigation guard.
// useAuthStore() is called inside the callback (not at module level)
// so it runs after pinia is initialized in main.ts.
router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    // Preserve the intended destination so login can redirect back
    return { name: 'login', query: { redirect: to.fullPath } }
  }
})

export default router
