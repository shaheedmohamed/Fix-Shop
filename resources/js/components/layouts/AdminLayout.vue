<template>
  <div class="admin-wrap">
    <!-- Sidebar -->
    <aside class="admin-aside">
      <div class="brand d-flex align-items-center mb-3">
        <img src="/images/2-removebg-preview.png" alt="Smart Path" class="brand-logo me-2" />
        <div class="fw-bold">Smart Path</div>
      </div>

      <div class="menu">
        <RouterLink :to="{ name: 'admin-dashboard' }" class="menu-item" :class="{ active: routeName==='admin-dashboard' }">
          <i class="fa-solid fa-house me-2"></i>
          <span>Admin Dashboard</span>
        </RouterLink>
        <div class="menu-section">POS Management</div>
        <RouterLink :to="{ name: 'admin-users' }" class="menu-item" :class="{ active: routeName==='admin-users' }">
          <i class="fa-solid fa-users me-2"></i>
          <span>Users</span>
        </RouterLink>
        <RouterLink :to="{ name: 'admin-subjects' }" class="menu-item" :class="{ active: routeName==='admin-subjects' }">
          <i class="fa-solid fa-book-open me-2"></i>
          <span>Subjects</span>
        </RouterLink>
        <RouterLink :to="{ name: 'admin-books' }" class="menu-item" :class="{ active: routeName==='admin-books' }">
          <i class="fa-solid fa-book me-2"></i>
          <span>Books</span>
        </RouterLink>
        <RouterLink :to="{ name: 'admin-logs' }" class="menu-item" :class="{ active: routeName==='admin-logs' }">
          <i class="fa-solid fa-list me-2"></i>
          <span>Logs</span>
        </RouterLink>
        <RouterLink :to="{ name: 'admin-settings' }" class="menu-item" :class="{ active: routeName==='admin-settings' }">
          <i class="fa-solid fa-gear me-2"></i>
          <span>Settings</span>
        </RouterLink>
      </div>
    </aside>

    <!-- Main -->
    <main class="admin-main">
      <header class="admin-header">
        <div>
          <div class="welcome">Welcome Back, {{ userName }} Admin</div>
        </div>
        <div class="header-actions">
          <span class="lang">EN ▾</span>
          <button class="icon-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
          <RouterLink :to="{ name: 'messages' }" class="icon-btn position-relative" title="Messages">
            <i class="fa-regular fa-message"></i>
            <span v-if="store.state.unreadCount>0" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ store.state.unreadCount }}</span>
          </RouterLink>
          <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
          <div class="avatar initials" :title="userName">{{ initials }}</div>
        </div>
      </header>

      <section class="admin-content">
        <router-view />
      </section>
    </main>
  </div>
</template>

<script>
import { computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import auth from '../../store/auth'
import store from '../../store/messages'

export default {
  name: 'AdminLayout',
  setup(){
    const route = useRoute()
    const userName = computed(() => auth.state.user?.name || 'Smart Path')
    const initials = computed(() => {
      const name = auth.state.user?.name?.trim() || 'Smart Path'
      const parts = name.split(/\s+/)
      let letters = parts.map(p => p[0] || '').join('')
      if (letters.length < 2) letters = (name.slice(0,2))
      return letters.slice(0,2).toUpperCase()
    })
    const routeName = computed(() => route.name)
    let unreadTimer = null
    onMounted(() => {
      store.refreshUnread()
      unreadTimer = setInterval(() => store.refreshUnread(), 10000)
    })
    onBeforeUnmount(() => { if (unreadTimer) clearInterval(unreadTimer) })
    return { userName, initials, routeName, store }
  }
}
</script>

<style scoped>
.admin-wrap { display: grid; grid-template-columns: 260px 1fr; min-height: calc(100vh - 72px); background: #f8fafc; }
@media (max-width: 991.98px){ .admin-wrap { grid-template-columns: 1fr; } .admin-aside { position: sticky; top: 64px; z-index: 10; } }

.admin-aside { padding: 16px; border-right: 1px solid rgba(2,6,23,.06); background: #fff; }
.brand-logo { height: 28px; }
.menu { display: flex; flex-direction: column; gap: 6px; }
.menu-section { margin: 10px 0 4px; font-size: .8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; }
.menu-item { display: flex; align-items: center; padding: 10px 12px; border-radius: 10px; color: #0f172a; text-decoration: none; }
.menu-item:hover { background: #f1f5f9; }
.menu-item.active { background: #fde68a; color: #111827; }

.admin-main { padding: 16px 20px; }
.admin-header { background: #fff; border: 1px solid rgba(2,6,23,.06); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; }
.welcome { font-weight: 700; color: #0f172a; }
.muted { color: #6b7280; font-size: .9rem; }
.header-actions { display: flex; align-items: center; gap: 10px; }
.lang { font-size: .85rem; color: #475569; background: #f1f5f9; border: 1px solid rgba(2,6,23,.06); padding: 6px 8px; border-radius: 8px; }
.icon-btn { width: 36px; height: 36px; border-radius: 10px; border: 1px solid rgba(2,6,23,.06); background: #fff; display: grid; place-items: center; color: #0f172a; }
.icon-btn:hover { background: #f8fafc; }
.avatar { width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 1px solid rgba(2,6,23,.06); }
.avatar img { width: 100%; height: 100%; object-fit: cover; }
.avatar.initials { display: flex; align-items: center; justify-content: center; background: #ef4444; color: #fff; font-weight: 800; letter-spacing: .5px; }

.admin-content { margin-top: 14px; }
</style>
