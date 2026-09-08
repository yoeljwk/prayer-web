export type RoomStatus = 'live' | 'scheduled' | 'ended'
export type RoomVisibility = 'public' | 'group' | 'private'
export type RoomParticipantRole = 'host' | 'co-host' | 'participant'

export interface RoomParticipant {
  id: string
  name: string
  username?: string
  avatar?: string
  role: RoomParticipantRole
  isPraying?: boolean
  hasRaisedHand?: boolean
  joinedAt: string
}

export interface PrayerRoom {
  id: string
  code: string
  name: string
  description: string
  hostName: string
  hostAvatar?: string
  status: RoomStatus
  visibility: RoomVisibility
  groupName?: string
  scheduledAt: string
  durationMinutes?: number
  currentParticipants: number
  maxParticipants: number
  hasReminderSet?: boolean
  currentFocusTopic: string
  scriptureVerse: {
    text: string
    passage: string
  }
  rules: string[]
  participants: RoomParticipant[]
}
