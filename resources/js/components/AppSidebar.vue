<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, ref, onMounted, watch, nextTick } from 'vue'
import {
    LayoutGrid, Folder, CheckCircle,
    Settings, Users, Calendar,
    ChevronRight,
} from 'lucide-vue-next'
import NavUser from '@/components/NavUser.vue'
import {
    Sidebar, SidebarContent, SidebarFooter,
    SidebarHeader, SidebarMenu, SidebarMenuItem,
    SidebarMenuButton,
} from '@/components/ui/sidebar'
import type { NavItem } from '@/types'
import { useSidebar } from '@/components/ui/sidebar'

const { state } = useSidebar()
const isCollapsed = computed(() => state.value === 'collapsed')

const page = usePage()
const userRoles = computed(() => (page.props.auth.user as any).roles ?? [])
const isAdmin = computed(() => userRoles.value.includes('admin'))
const isValidatorOrAdmin = computed(() =>
    userRoles.value.some((r: string) => ['admin', 'validator'].includes(r))
)
const isOperatorOrAdmin = computed(() =>
    userRoles.value.some((r: string) => ['admin', 'operator'].includes(r))
)

const mainNavItems = computed<NavItem[]>(() => {
    const all: NavItem[] = [
        { title: 'Dashboard', href: route('dashboard'), icon: LayoutGrid },
        { title: 'Bukti Dukung', href: route('bukti.index'), icon: Folder },
        { title: 'Validasi', href: route('validasi.index'), icon: CheckCircle },
    ]
    return all.filter(item => {
        if (item.title === 'Bukti Dukung') return isOperatorOrAdmin.value
        if (item.title === 'Validasi') return isValidatorOrAdmin.value
        return true
    })
})

const adminNavItems = computed<NavItem[]>(() => [
    { title: 'Pengaturan Aspek', href: route('aspek.index'), icon: Settings },
    { title: 'Manajemen User', href: route('users.index'), icon: Users },
    { title: 'Periode Penilaian', href: route('periode.index'), icon: Calendar },
])

const isActive = (href: string) => {
    const current = page.url
    const path = new URL(href, window.location.origin).pathname
    return current === path || current.startsWith(path + '/')
}

const navRef = ref<HTMLElement | null>(null)
const pillStyle = ref({ top: '0px', height: '0px', opacity: '0' })

const updatePill = () => {
    nextTick(() => {
        if (!navRef.value) return
        const active = navRef.value.querySelector('.nav-item-active') as HTMLElement | null
        if (!active) {
            pillStyle.value.opacity = '0'
            return
        }
        const containerTop = navRef.value.getBoundingClientRect().top
        const itemTop = active.getBoundingClientRect().top
        pillStyle.value = {
            top: (itemTop - containerTop) + 'px',
            height: active.offsetHeight + 'px',
            opacity: '1',
        }
    })
}

onMounted(updatePill)
watch(() => page.url, updatePill)
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                        <rect x="2" y="2" width="7" height="7" rx="1.5" fill="#7F77DD"/>
                                        <rect x="11" y="2" width="7" height="7" rx="1.5" fill="#AFA9EC"/>
                                        <rect x="2" y="11" width="7" height="7" rx="1.5" fill="#AFA9EC"/>
                                        <rect x="11" y="11" width="7" height="7" rx="1.5" fill="#534AB7"/>
                                    </svg>
                                </div>
                                <div class="flex flex-col leading-tight">
                                    <span class="font-semibold text-sm text-gray-900 font-[Inter]">PEKPPP</span>
                                    <span class="text-xs text-gray-400 font-[Inter]">Sistem Bukti Dukung</span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Menu section -->
            <div class="px-3 py-2">
                <p v-if="!isCollapsed" class="text-xs text-gray-400 uppercase tracking-widest mb-2 px-1 font-[Inter]">Menu</p>
                <div class="relative flex flex-col gap-1" ref="navRef">
                    
                    <Link
                        v-for="item in (mainNavItems ?? [])"
                        :key="item.title"
                        :href="item.href"
                        :class="[
                            'nav-item relative z-10 flex items-center rounded-xl transition-colors duration-150',
                            isCollapsed ? 'justify-center p-1' : 'gap-3 px-2 py-2',
                            !isCollapsed && isActive(item.href) ? 'nav-item-active bg-white shadow-sm border border-gray-100' : (!isCollapsed ? 'hover:bg-gray-50' : '')
                        ]"
                    >
                        <div :class="[
                            'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-300',
                            isActive(item.href) ? 'bg-orange-500' : 'bg-white shadow-sm border border-gray-100'
                        ]">
                            <component :is="item.icon" :class="['w-4 h-4', isActive(item.href) ? 'text-white' : 'text-gray-900']" />
                        </div>
                        <span v-if="!isCollapsed" class="text-sm font-semibold text-gray-900 font-[Inter]">{{ item.title }}</span>
                    </Link>
                </div>
            </div>

            <!-- Admin section -->
            <div v-if="isAdmin" class="px-3 py-2 mt-2">
                <p v-if="!isCollapsed" class="text-xs text-gray-400 uppercase tracking-widest mb-2 px-1 font-[Inter]">Admin</p>
                <div class="relative flex flex-col gap-1">
                    <Link
                        v-for="item in (adminNavItems ?? [])"
                        :key="item.title"
                        :href="item.href"
                        :class="[
                            'relative z-10 flex items-center rounded-xl transition-colors duration-150',
                            isCollapsed ? 'justify-center p-1' : 'gap-3 px-2 py-2',
                            !isCollapsed && isActive(item.href) ? 'bg-white shadow-sm border border-gray-100' : (!isCollapsed ? 'hover:bg-gray-50' : '')
                        ]"
                    >
                        <div :class="[
                            'w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-all duration-300',
                            isActive(item.href) ? 'bg-orange-500' : 'bg-white shadow-sm border border-gray-100'
                        ]">
                            <component :is="item.icon" :class="['w-4 h-4', isActive(item.href) ? 'text-white' : 'text-gray-900']" />
                        </div>
                        <span v-if="!isCollapsed" class="text-sm font-semibold text-gray-900 font-[Inter]">{{ item.title }}</span>
                    </Link>
                </div>
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>