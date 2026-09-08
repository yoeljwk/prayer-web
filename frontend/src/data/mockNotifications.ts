import { reactive, computed } from 'vue'
import type { NotificationItem, NotificationPreferences } from '@/types'

export const mockNotificationsState: NotificationItem[] = reactive([
  {
    id: 'notif-1',
    title: 'Seseorang mendoakan permohonanmu',
    description: 'Permohonan doa yang kamu bagikan telah didoakan oleh seseorang.',
    category: 'prayer',
    timestamp: '10 menit lalu',
    isRead: false,
    link: '/permohonan-doa',
  },
  {
    id: 'notif-2',
    title: 'Komentar baru',
    description: 'Sarah memberikan dukungan pada permohonan doamu.',
    category: 'prayer',
    timestamp: '1 jam lalu',
    isRead: false,
    link: '/permohonan-doa',
  },
  {
    id: 'notif-3',
    title: 'Permintaan diterima',
    description: 'Permintaanmu untuk bergabung dengan Grace Community telah diterima.',
    category: 'community',
    timestamp: '3 jam lalu',
    isRead: false,
    link: '/komunitas/grace-community',
  },
  {
    id: 'notif-4',
    title: 'Ruang doa akan segera dimulai',
    description: 'Doa Malam akan dimulai dalam 15 menit.',
    category: 'room',
    timestamp: '4 jam lalu',
    isRead: false,
    link: '/ruang-doa/live-01',
  },
  {
    id: 'notif-5',
    title: 'Doa ditandai terjawab',
    description: 'Salah satu doa dalam komunitasmu telah ditandai sebagai doa terjawab.',
    category: 'prayer',
    timestamp: '1 hari lalu',
    isRead: true,
    link: '/permohonan-doa',
  },
  {
    id: 'notif-6',
    title: 'Pengumuman komunitas baru',
    description: 'Jonathan membagikan jadwal doa minggu ini di Doa Malam.',
    category: 'community',
    timestamp: '2 hari lalu',
    isRead: true,
    link: '/komunitas/doa-malam',
  },
])

export const mockNotificationPreferences: NotificationPreferences = reactive({
  prayerPrayed: true,
  newComments: true,
  communityActivity: true,
  roomReminder: true,
})

// Unread count computed
export const unreadNotificationCount = computed(() => {
  return mockNotificationsState.filter((n) => !n.isRead).length
})

// Helper to mark single notification as read
export function markNotificationAsRead(id: string) {
  const notif = mockNotificationsState.find((n) => n.id === id)
  if (notif) {
    notif.isRead = true
  }
}

// Helper to mark all notifications as read
export function markAllNotificationsAsRead() {
  mockNotificationsState.forEach((n) => {
    n.isRead = true
  })
}
