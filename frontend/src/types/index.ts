// Centralized TypeScript Types & Interfaces

export interface NavItem {
  name: string
  path: string
}

export interface FeatureCardItem {
  id: string
  title: string
  description: string
  link: string
}

export interface User {
  id: number | string
  name: string
  username?: string
  email: string
  avatar?: string
}

export interface ApiResponse<T = unknown> {
  success: boolean
  message: string
  data: T
}

export type PrayerVisibility = 'public' | 'group' | 'private'
export type PrayerStatus = 'active' | 'answered'

export interface PrayerComment {
  id: string
  authorName: string
  authorAvatar?: string
  isAnonymous: boolean
  createdAt: string
  content: string
}

export interface PrayerRequest {
  id: string
  authorName: string
  authorAvatar?: string
  isAnonymous: boolean
  createdAt: string
  content: string
  visibility: PrayerVisibility
  groupName?: string
  status: PrayerStatus
  answeredAt?: string
  prayerCount: number
  hasPrayed?: boolean
  comments: PrayerComment[]
  isOwner?: boolean
}

export * from './community'
export * from './room'
export * from './notification'




