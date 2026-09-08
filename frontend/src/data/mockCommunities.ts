import { reactive } from 'vue'
import type { Community } from '@/types'

export const mockCommunitiesState: Community[] = reactive([
  {
    id: 'comm-1',
    slug: 'doa-malam',
    name: 'Doa Malam',
    description: 'Wadah persekutuan doa malam bersama setiap hari jam 21:00 WIB untuk saling menguatkan dalam iman.',
    avatar: 'https://images.unsplash.com/photo-1519817650390-64a93db51149?w=150&auto=format&fit=crop&q=80',
    visibility: 'public',
    memberCount: 142,
    joinStatus: 'joined',
    createdAt: '12 Jan 2024',
    members: [
      { id: 'm-1', name: 'Jonathan Edward', username: 'jonathan', role: 'owner', joinedAt: '12 Jan 2024' },
      { id: 'm-2', name: 'Maria Grace', username: 'mariagrace', role: 'admin', joinedAt: '15 Jan 2024' },
      { id: 'm-3', name: 'Youwel Ginting', username: 'yoelginting', role: 'member', joinedAt: '01 Feb 2024' },
      { id: 'm-4', name: 'Daniel Setiawan', username: 'daniel', role: 'member', joinedAt: '10 Feb 2024' },
      { id: 'm-5', name: 'Sarah Amanda', username: 'sarah', role: 'member', joinedAt: '20 Feb 2024' },
    ],
    activities: [
      {
        id: 'act-1',
        type: 'announcement',
        title: 'Jadwal Doa Bersama Minggu Ini',
        content: 'Mari bergabung dalam doa syafaat bersama setiap pukul 21:00 WIB via Zoom / Ruang Doa.',
        authorName: 'Jonathan Edward',
        createdAt: '2 jam lalu',
      },
      {
        id: 'act-2',
        type: 'prayer_shared',
        content: 'Maria Grace membagikan permohonan doa baru untuk pemulihan kesehatan keluarga.',
        authorName: 'Maria Grace',
        createdAt: '5 jam lalu',
      },
      {
        id: 'act-3',
        type: 'member_joined',
        content: 'Sarah Amanda baru saja bergabung dengan Komunitas Doa Malam.',
        authorName: 'Sarah Amanda',
        createdAt: '1 hari lalu',
      },
    ],
    prayers: [
      {
        id: 'cp-1',
        authorName: 'Jonathan Edward',
        isAnonymous: false,
        createdAt: '3 jam lalu',
        content: 'Mohon dukungan doa untuk kelancaran pelayanan retret pemuda akhir bulan ini agar banyak hati dipulihkan.',
        visibility: 'group',
        groupName: 'Doa Malam',
        status: 'active',
        prayerCount: 18,
        hasPrayed: true,
        comments: [
          { id: 'cc-1', authorName: 'Maria Grace', isAnonymous: false, createdAt: '2 jam lalu', content: 'Amin! Kami ikut mendoakan kelancaran retret.' },
        ],
      },
      {
        id: 'cp-2',
        authorName: 'Anonim',
        isAnonymous: true,
        createdAt: '1 hari lalu',
        content: 'Mohon doa untuk keputusan karir dan tempat kerja baru yang sedang saya gumulkan.',
        visibility: 'group',
        groupName: 'Doa Malam',
        status: 'active',
        prayerCount: 12,
        hasPrayed: false,
        comments: [],
      },
    ],
  },
  {
    id: 'comm-2',
    slug: 'pemuda-gereja',
    name: 'Pemuda Gereja',
    description: 'Komunitas pemuda-pemudi gereja yang rindu bertumbuh dalam firman, persekutuan, dan pelayanan.',
    avatar: 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=150&auto=format&fit=crop&q=80',
    visibility: 'public',
    memberCount: 88,
    joinStatus: 'not_joined',
    createdAt: '05 Feb 2024',
    members: [
      { id: 'm-6', name: 'David Christian', username: 'davidc', role: 'owner', joinedAt: '05 Feb 2024' },
      { id: 'm-7', name: 'Rachel Wijaya', username: 'rachel', role: 'admin', joinedAt: '06 Feb 2024' },
      { id: 'm-8', name: 'Kevin Pratama', username: 'kevin', role: 'member', joinedAt: '12 Feb 2024' },
    ],
    activities: [
      {
        id: 'act-4',
        type: 'announcement',
        title: 'Youth Fellowship & Sharing Session',
        content: 'Sabtu ini kita ada fellowship pemuda jam 17:00 WIB. Sampai jumpa teman-teman!',
        authorName: 'David Christian',
        createdAt: '1 hari lalu',
      },
    ],
    prayers: [
      {
        id: 'cp-3',
        authorName: 'Rachel Wijaya',
        isAnonymous: false,
        createdAt: '2 hari lalu',
        content: 'Mari doakan teman-teman pemuda yang sedang menghadapi ujian akhir semester dan pencarian kerja.',
        visibility: 'group',
        groupName: 'Pemuda Gereja',
        status: 'active',
        prayerCount: 24,
        hasPrayed: false,
        comments: [],
      },
    ],
  },
  {
    id: 'comm-3',
    slug: 'grace-community',
    name: 'Grace Community',
    description: 'Grup pendalaman Alkitab & ruang saling mendukung secara personal dan tertutup.',
    avatar: 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=150&auto=format&fit=crop&q=80',
    visibility: 'private',
    memberCount: 35,
    maxMembers: 50,
    joinStatus: 'pending',
    createdAt: '20 Jan 2024',
    members: [
      { id: 'm-9', name: 'Esther Kurniawan', username: 'esther', role: 'owner', joinedAt: '20 Jan 2024' },
      { id: 'm-10', name: 'Samuel Hartono', username: 'samuel', role: 'admin', joinedAt: '22 Jan 2024' },
    ],
    activities: [
      {
        id: 'act-5',
        type: 'announcement',
        title: 'Diskusi Kitab Efesus',
        content: 'Minggu ini kita masuk bab 3. Mohon persiapkan pembacaan terlebih dahulu.',
        authorName: 'Esther Kurniawan',
        createdAt: '3 hari lalu',
      },
    ],
    prayers: [],
  },
  {
    id: 'comm-4',
    slug: 'komunitas-kampus',
    name: 'Komunitas Kampus',
    description: 'Persekutuan mahasiswa antar kampus untuk saling menopang dalam studi dan kesaksian iman.',
    avatar: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=150&auto=format&fit=crop&q=80',
    visibility: 'public',
    memberCount: 210,
    joinStatus: 'not_joined',
    createdAt: '01 Des 2023',
    members: [
      { id: 'm-11', name: 'Michael Tan', username: 'michaeltan', role: 'owner', joinedAt: '01 Des 2023' },
      { id: 'm-12', name: 'Jessica Lee', username: 'jessica', role: 'member', joinedAt: '05 Des 2023' },
    ],
    activities: [],
    prayers: [],
  },
  {
    id: 'comm-5',
    slug: 'keluarga-doa',
    name: 'Keluarga Doa',
    description: 'Komunitas doa antar keluarga untuk saling mendoakan kehidupan rumah tangga dan anak-anak.',
    avatar: 'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=150&auto=format&fit=crop&q=80',
    visibility: 'private',
    memberCount: 12,
    maxMembers: 20,
    joinStatus: 'joined',
    createdAt: '15 Feb 2024',
    members: [
      { id: 'm-13', name: 'Bpk. Hendra & Ibu Ruth', username: 'hendraruth', role: 'owner', joinedAt: '15 Feb 2024' },
      { id: 'm-14', name: 'Youwel Ginting', username: 'yoelginting', role: 'member', joinedAt: '18 Feb 2024' },
    ],
    activities: [
      {
        id: 'act-6',
        type: 'announcement',
        title: 'Doa Syukuran Keluarga',
        content: 'Puji Tuhan untuk kesehatan dan pemeliharaan Tuhan bagi keluarga kita semua.',
        authorName: 'Bpk. Hendra & Ibu Ruth',
        createdAt: '4 hari lalu',
      },
    ],
    prayers: [],
  },
  {
    id: 'comm-6',
    slug: 'morning-prayer-circle',
    name: 'Morning Prayer Circle',
    description: 'Start your day with prayer. Waktu doa singkat setiap jam 06:00 pagi sebelum beraktivitas.',
    avatar: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=150&auto=format&fit=crop&q=80',
    visibility: 'public',
    memberCount: 64,
    joinStatus: 'not_joined',
    createdAt: '28 Jan 2024',
    members: [
      { id: 'm-15', name: 'Grace Natalia', username: 'gracenat', role: 'owner', joinedAt: '28 Jan 2024' },
    ],
    activities: [],
    prayers: [],
  },
  {
    id: 'comm-7',
    slug: 'komunitas-alumni',
    name: 'Komunitas Alumni',
    description: 'Jaringan doa dan persekutuan profesional muda serta alumni kampus.',
    avatar: 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=150&auto=format&fit=crop&q=80',
    visibility: 'private',
    memberCount: 45,
    joinStatus: 'not_joined',
    createdAt: '10 Nov 2023',
    members: [
      { id: 'm-16', name: 'Stephanus Yudi', username: 'stephanus', role: 'owner', joinedAt: '10 Nov 2023' },
    ],
    activities: [],
    prayers: [],
  },
  {
    id: 'comm-8',
    slug: 'doa-fajar',
    name: 'Doa Fajar',
    description: 'Persekutuan doa subuh jam 05:00 WIB untuk menaikkan puji-pujian dan permohonan di awal hari.',
    avatar: 'https://images.unsplash.com/photo-1470240731273-7821a6eeb6bd?w=150&auto=format&fit=crop&q=80',
    visibility: 'public',
    memberCount: 175,
    joinStatus: 'not_joined',
    createdAt: '01 Jan 2024',
    members: [
      { id: 'm-17', name: 'Pastor Agus', username: 'pastoragus', role: 'owner', joinedAt: '01 Jan 2024' },
    ],
    activities: [],
    prayers: [],
  },
])

// Helper to toggle join status
export function toggleCommunityJoin(slug: string) {
  const comm = mockCommunitiesState.find((c) => c.slug === slug)
  if (!comm) return

  if (comm.joinStatus === 'joined') {
    comm.joinStatus = 'not_joined'
    comm.memberCount = Math.max(0, comm.memberCount - 1)
  } else if (comm.joinStatus === 'pending') {
    comm.joinStatus = 'not_joined'
  } else if (comm.visibility === 'public') {
    comm.joinStatus = 'joined'
    comm.memberCount += 1
  } else {
    comm.joinStatus = 'pending'
  }
}

// Helper to add new community
export function addCommunity(newComm: Omit<Community, 'id' | 'slug' | 'memberCount' | 'joinStatus' | 'createdAt' | 'members' | 'activities' | 'prayers'>) {
  const id = `comm-${Date.now()}`
  const slug = newComm.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '') || `comm-${Date.now()}`
  
  const created: Community = {
    ...newComm,
    id,
    slug,
    memberCount: 1,
    joinStatus: 'joined',
    createdAt: 'Baru saja',
    members: [
      {
        id: `m-${Date.now()}`,
        name: 'Youwel Ginting',
        username: 'yoelginting',
        role: 'owner',
        joinedAt: 'Baru saja',
      },
    ],
    activities: [
      {
        id: `act-${Date.now()}`,
        type: 'announcement',
        title: 'Komunitas Dibuat',
        content: `Selamat datang di komunitas ${newComm.name}!`,
        authorName: 'Youwel Ginting',
        createdAt: 'Baru saja',
      },
    ],
    prayers: [],
  }

  mockCommunitiesState.unshift(created)
  return created
}
