import { reactive } from 'vue'
import type { PrayerRequest } from '@/types'

export const initialMockPrayers: PrayerRequest[] = []

export const mockPrayersState = reactive<PrayerRequest[]>([])

export const mockGroups = [
  'Komunitas Pemuda',
  'Komunitas Doa Malam',
  'Komunitas Keluarga Muda',
  'Komunitas Profesional',
]
