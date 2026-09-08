import type { PrayerRequest } from './index'

export type CommunityVisibility = 'public' | 'private'
export type JoinStatus = 'not_joined' | 'pending' | 'joined'
export type MemberRole = 'owner' | 'admin' | 'member'

export interface CommunityMember {
  id: string
  userId?: string
  name: string
  username?: string
  avatar?: string
  role: MemberRole
  joinedAt: string
}

export interface CommunityActivity {
  id: string
  type: 'announcement' | 'member_joined' | 'prayer_shared'
  title?: string
  content: string
  authorName: string
  authorAvatar?: string
  createdAt: string
}

export interface Community {
  id: string
  slug: string
  name: string
  description: string
  avatar: string
  banner?: string
  visibility: CommunityVisibility
  memberCount: number
  maxMembers?: number
  joinStatus: JoinStatus
  userRole?: MemberRole | null
  inviteCode?: string
  createdAt: string
  members: CommunityMember[]
  activities: CommunityActivity[]
  prayers: PrayerRequest[]
}
