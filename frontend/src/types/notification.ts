export type NotificationCategory = 'prayer' | 'community' | 'room'

export interface NotificationItem {
  id: string
  title: string
  description: string
  category: NotificationCategory
  timestamp: string
  isRead: boolean
  link?: string
}

export interface NotificationPreferences {
  prayerPrayed: boolean
  newComments: boolean
  communityActivity: boolean
  roomReminder: boolean
}
