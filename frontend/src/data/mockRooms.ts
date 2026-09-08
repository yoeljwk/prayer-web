import { reactive } from 'vue'
import type { PrayerRoom } from '@/types'

export const mockRoomsState: PrayerRoom[] = reactive([
  {
    id: 'room-1',
    code: 'live-01',
    name: 'Doa Syafaat Pemulihan',
    description: 'Ruang doa bersama untuk saling mendoakan beban kesehatan, pemulihan keluarga, dan pergumulan hidup.',
    hostName: 'Jonathan Edward',
    status: 'live',
    visibility: 'public',
    scheduledAt: 'Hari ini, 21:00 WIB',
    durationMinutes: 45,
    currentParticipants: 8,
    maxParticipants: 15,
    hasReminderSet: false,
    currentFocusTopic: 'Mendoakan Kesehatan & Pemulihan Jiwa Saudara-Saudari Kita',
    scriptureVerse: {
      text: 'Sebab di mana dua atau tiga orang berkumpul dalam Nama-Ku, di situ Aku ada di tengah-tengah mereka.',
      passage: 'Matius 18:20',
    },
    rules: [
      'Menjaga ketenangan dan suasana hati yang terarah pada Tuhan.',
      'Saling menghormati saat bergantian menyampaikan pokok doa.',
      'Menjaga kerahasiaan isi doa saudara-saudari seiman.',
    ],
    participants: [
      { id: 'rp-1', name: 'Jonathan Edward', username: 'jonathan', role: 'host', isPraying: true, hasRaisedHand: false, joinedAt: '21:00' },
      { id: 'rp-2', name: 'Maria Grace', username: 'mariagrace', role: 'co-host', isPraying: true, hasRaisedHand: false, joinedAt: '21:02' },
      { id: 'rp-3', name: 'Youwel Ginting', username: 'yoelginting', role: 'participant', isPraying: false, hasRaisedHand: false, joinedAt: '21:05' },
      { id: 'rp-4', name: 'David Christian', username: 'davidc', role: 'participant', isPraying: true, hasRaisedHand: true, joinedAt: '21:06' },
      { id: 'rp-5', name: 'Esther Kurniawan', username: 'esther', role: 'participant', isPraying: false, hasRaisedHand: false, joinedAt: '21:10' },
      { id: 'rp-6', name: 'Samuel Hartono', username: 'samuel', role: 'participant', isPraying: false, hasRaisedHand: false, joinedAt: '21:12' },
      { id: 'rp-7', name: 'Rachel Wijaya', username: 'rachel', role: 'participant', isPraying: true, hasRaisedHand: false, joinedAt: '21:15' },
      { id: 'rp-8', name: 'Grace Natalia', username: 'gracenat', role: 'participant', isPraying: false, hasRaisedHand: false, joinedAt: '21:18' },
    ],
  },
  {
    id: 'room-2',
    code: 'live-02',
    name: 'Persekutuan Doa Malam Pemuda',
    description: 'Saat hening dan berdoa bersama pemuda-pemudi gereja dalam keheningan malam.',
    hostName: 'David Christian',
    status: 'live',
    visibility: 'group',
    groupName: 'Pemuda Gereja',
    scheduledAt: 'Hari ini, 21:30 WIB',
    durationMinutes: 30,
    currentParticipants: 5,
    maxParticipants: 12,
    hasReminderSet: false,
    currentFocusTopic: 'Mendoakan Generasi Muda & Keputusan Karir/Studi',
    scriptureVerse: {
      text: 'Janganlah hendaknya kamu kuatir tentang apapun juga, tetapi nyatakanlah dalam segala hal keinginanmu kepada Allah dalam doa dan permohonan dengan ucapan syukur.',
      passage: 'Filipi 4:6',
    },
    rules: [
      'Hadir dengan sikap berserah dan siap saling menguatkan.',
      'Gunakan tombol Angkat Tangan jika ingin menaikkan doa secara pribadi.',
    ],
    participants: [
      { id: 'rp-9', name: 'David Christian', username: 'davidc', role: 'host', isPraying: true, hasRaisedHand: false, joinedAt: '21:30' },
      { id: 'rp-10', name: 'Kevin Pratama', username: 'kevin', role: 'participant', isPraying: true, hasRaisedHand: false, joinedAt: '21:31' },
      { id: 'rp-11', name: 'Sarah Amanda', username: 'sarah', role: 'participant', isPraying: false, hasRaisedHand: false, joinedAt: '21:32' },
    ],
  },
  {
    id: 'room-3',
    code: 'full-01',
    name: 'Doa Intim Keluarga & Pasangan',
    description: 'Ruang doa tertutup dengan jumlah tempat terbatas untuk komunitas Keluarga Doa.',
    hostName: 'Bpk. Hendra & Ibu Ruth',
    status: 'live',
    visibility: 'private',
    groupName: 'Keluarga Doa',
    scheduledAt: 'Hari ini, 20:00 WIB',
    durationMinutes: 60,
    currentParticipants: 4,
    maxParticipants: 4,
    hasReminderSet: false,
    currentFocusTopic: 'Keharmonisan Rumah Tangga & Pertumbuhan Anak-Anak',
    scriptureVerse: {
      text: 'Tetapi aku dan seisi rumahku, kami akan beribadah kepada TUHAN!',
      passage: 'Yosua 24:15b',
    },
    rules: [
      'Ruang doa khusus komunitas privat terdaftar.',
    ],
    participants: [
      { id: 'rp-12', name: 'Bpk. Hendra', role: 'host', joinedAt: '20:00' },
      { id: 'rp-13', name: 'Ibu Ruth', role: 'co-host', joinedAt: '20:00' },
      { id: 'rp-14', name: 'Bpk. Yudi', role: 'participant', joinedAt: '20:02' },
      { id: 'rp-15', name: 'Ibu Ani', role: 'participant', joinedAt: '20:05' },
    ],
  },
  {
    id: 'room-4',
    code: 'sched-01',
    name: 'Morning Worship & Prayer Circle',
    description: 'Menyerahkan hari yang baru ke dalam tangan Tuhan sebelum memulai kesibukan.',
    hostName: 'Grace Natalia',
    status: 'scheduled',
    visibility: 'public',
    scheduledAt: 'Besok, 06:00 WIB',
    durationMinutes: 30,
    currentParticipants: 0,
    maxParticipants: 20,
    hasReminderSet: true,
    currentFocusTopic: 'Penyerahan Langkah Hidup & Rasa Syukur Pagi Hari',
    scriptureVerse: {
      text: 'TUHAN, pada waktu pagi Engkau mendengar suaraku, pada waktu pagi aku mengatur kurban bagi-Mu, dan aku menunggu-nunggu.',
      passage: 'Mazmur 5:4',
    },
    rules: [
      'Hadir tepat waktu sebelum doa dimulai pukul 06:00 WIB.',
    ],
    participants: [],
  },
  {
    id: 'room-5',
    code: 'sched-02',
    name: 'Doa Fajar Syafaat Minggu Ini',
    description: 'Doa fajar mingguan bersama seluruh anggota komunitas gereja dan umum.',
    hostName: 'Pastor Agus',
    status: 'scheduled',
    visibility: 'public',
    scheduledAt: 'Minggu, 05:00 WIB',
    durationMinutes: 45,
    currentParticipants: 0,
    maxParticipants: 50,
    hasReminderSet: false,
    currentFocusTopic: 'Syafaat Bagi Kota & Bangsa',
    scriptureVerse: {
      text: 'Carilah kesejahteraan kota ke mana kamu Aku buang, dan berdoalah untuk kota itu kepada TUHAN.',
      passage: 'Yeremia 29:7',
    },
    rules: [
      'Nyalakan hati yang siap bersyafaat dalam kerendahan hati.',
    ],
    participants: [],
  },
  {
    id: 'room-6',
    code: 'ended-01',
    name: 'Doa Ucapan Syukur Akhir Bulan',
    description: 'Sesi doa bersama mengucap syukur atas penyertaan Tuhan sepanjang bulan lalu.',
    hostName: 'Jonathan Edward',
    status: 'ended',
    visibility: 'public',
    scheduledAt: 'Kemarin, 20:00 WIB',
    durationMinutes: 60,
    currentParticipants: 18,
    maxParticipants: 30,
    hasReminderSet: false,
    currentFocusTopic: 'Mengucap Syukur Atas Kebaikan Tuhan',
    scriptureVerse: {
      text: 'Mengucap syukurlah dalam segala hal, sebab itulah yang dikehendaki Allah di dalam Kristus Yesus bagi kamu.',
      passage: '1 Tesalonika 5:18',
    },
    rules: [],
    participants: [],
  },
  {
    id: 'room-7',
    code: 'ended-02',
    name: 'Retret Penutupan Persekutuan Doa',
    description: 'Sesi penutupan rangkaian retret doa malam.',
    hostName: 'Maria Grace',
    status: 'ended',
    visibility: 'group',
    groupName: 'Grace Community',
    scheduledAt: '3 Hari lalu, 19:00 WIB',
    durationMinutes: 90,
    currentParticipants: 14,
    maxParticipants: 20,
    hasReminderSet: false,
    currentFocusTopic: 'Penyerahan Pelayanan & Hati yang Diperbaharui',
    scriptureVerse: {
      text: 'Hati yang bersih ciptakanlah bagiku, ya Allah, dan perbaharuilah batinku dengan roh yang teguh!',
      passage: 'Mazmur 51:12',
    },
    rules: [],
    participants: [],
  },
])

// Helper to toggle reminder
export function toggleRoomReminder(code: string) {
  const room = mockRoomsState.find((r) => r.code === code)
  if (room) {
    room.hasReminderSet = !room.hasReminderSet
  }
}

// Helper to add new room
export function addPrayerRoom(newRoom: {
  name: string
  description: string
  visibility: 'public' | 'group' | 'private'
  groupName?: string
  scheduledAt: string
  durationMinutes?: number
  maxParticipants: number
  startNow: boolean
}) {
  const id = `room-${Date.now()}`
  const code = `room-${Math.floor(1000 + Math.random() * 9000)}`
  
  const created: PrayerRoom = {
    id,
    code,
    name: newRoom.name,
    description: newRoom.description,
    hostName: 'Youwel Ginting',
    status: newRoom.startNow ? 'live' : 'scheduled',
    visibility: newRoom.visibility,
    groupName: newRoom.groupName,
    scheduledAt: newRoom.startNow ? 'Baru saja dimulai' : newRoom.scheduledAt,
    durationMinutes: newRoom.durationMinutes || 30,
    currentParticipants: newRoom.startNow ? 1 : 0,
    maxParticipants: newRoom.maxParticipants,
    hasReminderSet: false,
    currentFocusTopic: 'Fokus Doa Syafaat & Persekutuan Bersama',
    scriptureVerse: {
      text: 'Sebab di mana dua atau tiga orang berkumpul dalam Nama-Ku, di situ Aku ada di tengah-tengah mereka.',
      passage: 'Matius 18:20',
    },
    rules: [
      'Menjaga ketenangan dan keheningan dalam persekutuan.',
      'Saling mendoakan dalam kasih.',
    ],
    participants: newRoom.startNow
      ? [
          {
            id: `rp-${Date.now()}`,
            name: 'Youwel Ginting',
            username: 'yoelginting',
            role: 'host',
            isPraying: true,
            hasRaisedHand: false,
            joinedAt: 'Baru saja',
          },
        ]
      : [],
  }

  mockRoomsState.unshift(created)
  return created
}
