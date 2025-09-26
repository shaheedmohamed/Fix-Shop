<template>
  <div class="nav-stack">
    <UtilityBar />
    <transition name="fade-slide">
    <nav
      class="sp-navbar sticky-top"
      :class="{ 'scrolled': isScrolled }"
      role="navigation"
      aria-label="Main"
    >
      <div class="container d-flex align-items-center justify-content-between">
        <!-- Brand -->
        <RouterLink class="brand d-flex align-items-center text-decoration-none" :to="{ name: 'home' }" @click="closeMenu">
          <i class="fa-solid fa-graduation-cap fs-4 me-2"></i>
          <span class="fw-bold">Fix & Shop</span>
        </RouterLink>

        <!-- Toggler -->
        <button class="menu-btn d-lg-none" :aria-expanded="isOpen ? 'true' : 'false'" @click="toggleMenu" aria-label="Toggle Menu">
          <span :class="{ open: isOpen }"></span>
          <span :class="{ open: isOpen }"></span>
          <span :class="{ open: isOpen }"></span>
        </button>

        <!-- Links -->
        <div class="links-wrapper" :class="{ open: isOpen }">
          <ul class="nav-list">
            <li><RouterLink class="nav-link" :to="{ name: 'home' }" @click="closeMenu">Home</RouterLink></li>
            <li><RouterLink class="nav-link" to="/posts" @click="closeMenu">Posts</RouterLink></li>
            <li><RouterLink class="nav-link" :to="{ name: 'about' }" @click="closeMenu">About</RouterLink></li>
            <li><RouterLink class="nav-link" :to="{ name: 'products' }" @click="closeMenu">Products</RouterLink></li>
            <li v-if="isAdminUser">
              <RouterLink class="nav-link admin-link" :to="{ name: 'admin-dashboard' }" @click="closeMenu">
                <i class="fa-solid fa-user-shield me-2"></i>
                Admin
              </RouterLink>
            </li>
          </ul>

          <!-- Search -->
          <form class="search-wrap d-none d-md-flex ms-2" @submit.prevent="submitSearch">
            <input v-model.trim="searchTerm" type="search" class="form-control form-control-sm" placeholder="Search products..." aria-label="Search products" />
            <button class="btn btn-sm btn-outline-primary ms-2" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          </form>

          <div class="auth-area">
            <template v-if="!isAuthed">
              <RouterLink class="btn btn-outline-primary me-2" :to="{ name: 'login' }" @click="closeMenu">Login</RouterLink>
              <RouterLink class="btn btn-primary" :to="{ name: 'register' }" @click="closeMenu">Register</RouterLink>
            </template>
            <template v-else>
              <RouterLink v-if="!isAdminUser" class="btn btn-outline-primary me-2" :to="dashboardRoute" @click="closeMenu">
                <i class="fa-solid fa-gauge-high me-2"></i>{{ dashboardLabel }}
              </RouterLink>
              <button class="btn btn-light position-relative me-2" @click="toggleCart" title="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span v-if="cartCount>0" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ cartCount }}</span>
              </button>
              <div class="position-relative me-2" ref="notifWrap">
                <button class="btn btn-light position-relative" @click="toggleNotif" title="Notifications" aria-haspopup="menu" :aria-expanded="notifOpen ? 'true' : 'false'">
                  <i class="fa-regular fa-bell"></i>
                  <span v-if="store.state.unreadCount>0" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ store.state.unreadCount }}</span>
                </button>
                <div v-if="notifOpen" class="notif-menu">
                  <div class="notif-header d-flex align-items-center justify-content-between">
                    <strong>Notifications</strong>
                    <small class="text-muted" v-if="store.state.unreadCount>0">{{ store.state.unreadCount }} unread</small>
                  </div>
                  <div v-if="store.state.convLoading" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>
                  <template v-else>
                    <div v-if="recentUnread.length===0" class="px-3 py-2 text-muted small">No new messages</div>
                    <button v-for="c in recentUnread" :key="c.id" class="notif-item" @click="openConv(c.id)">
                      <div class="small text-truncate">{{ conversationTitle(c) }}</div>
                      <div class="text-muted xsmall text-truncate" v-if="c.last_message">{{ c.last_message.content }}</div>
                    </button>
                    <div class="px-2 py-2 text-end">
                      <button class="btn btn-sm btn-outline-secondary" @click="openNotificationsCenter">Open messages</button>
                    </div>
                  </template>
                </div>
              </div>
              <div class="dropdown" ref="dropdownWrap">
                <button class="btn btn-light d-flex align-items-center" @click="toggleDropdown" :aria-expanded="dropdown ? 'true' : 'false'" aria-haspopup="menu">
                  <i class="fa-regular fa-user me-2"></i>{{ userName }}
                  <i class="fa-solid fa-chevron-down ms-2 small"></i>
                </button>
                <div class="dropdown-menu end" v-if="dropdown" role="menu">
                  <div class="dropdown-header">
                    <div class="fw-bold">{{ userName }}</div>
                    <small class="text-muted">{{ userRole === 'admin' ? 'Administrator' : userRole === 'provider' ? 'Provider' : 'Customer' }}</small>
                  </div>
                  <div class="dropdown-divider"></div>
                  <RouterLink v-if="userRole !== 'admin'" class="dropdown-item" :to="{ name: 'dashboard' }" @click="closeMenu">
                    <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
                  </RouterLink>
                  <RouterLink v-else class="dropdown-item" :to="{ name: 'admin-dashboard' }" @click="closeMenu">
                    <i class="fa-solid fa-user-shield me-2"></i>Admin Dashboard
                  </RouterLink>
                  <RouterLink class="dropdown-item" :to="{ name: 'profile' }" @click="closeMenu">
                    <i class="fa-regular fa-id-badge me-2"></i>My Profile
                  </RouterLink>
                  <RouterLink v-if="userRole === 'buyer'" class="dropdown-item" :to="{ name: 'orders' }" @click="closeMenu">
                    <i class="fa-solid fa-shopping-bag me-2"></i>My Orders
                  </RouterLink>
                  <RouterLink v-if="userRole === 'provider'" class="dropdown-item" :to="{ name: 'vendor-products' }" @click="closeMenu">
                    <i class="fa-solid fa-box me-2"></i>My Products
                  </RouterLink>
                  <RouterLink v-if="userRole === 'provider'" class="dropdown-item" :to="{ name: 'vendor-communications' }" @click="closeMenu">
                    <i class="fa-solid fa-comments me-2"></i>Communications
                  </RouterLink>
                  <div class="dropdown-divider"></div>
                  <RouterLink v-if="userRole==='admin'" class="dropdown-item" :to="{ name: 'admin-settings' }" @click="closeMenu">
                    <i class="fa-solid fa-cog me-2"></i>Settings
                  </RouterLink>
                  <button class="dropdown-item text-danger" @click="handleLogout">
                    <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                  </button>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
      <!-- subtle top glow -->
      <div class="top-glow"></div>
    </nav>
    </transition>
    <SecondaryNav />
  </div>
</template>

<script>
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import auth from '../../store/auth'
import store from '../../store/messages'
import cart from '../../store/cart'
import UtilityBar from './UtilityBar.vue'
import SecondaryNav from './SecondaryNav.vue'

export default {
  name: 'NavbarLayout',
  components: { UtilityBar, SecondaryNav },
  setup(_, { emit, expose }){
    const router = useRouter()
    const isOpen = ref(false)
    const dropdown = ref(false)
    const dropdownWrap = ref(null)
    const notifOpen = ref(false)
    const notifWrap = ref(null)
    const isScrolled = ref(false)
    const searchTerm = ref('')
    let unreadTimer = null

    const isAuthed = computed(() => auth.isAuthenticated())
    const userName = computed(() => auth.state.user?.name || 'Account')
    const userRole = computed(() => auth.state.user?.role || 'buyer')
    const isAdminUser = computed(() => {
      const u = auth.state.user || {}
      return !!(u.is_admin || u.isAdmin || u.role === 'admin' || u.role === 'superadmin')
    })
    const role = computed(() => auth.state.user?.role)
    const dashboardRoute = computed(() => {
      if (role.value === 'parent') return { name: 'parent-dashboard' }
      if (role.value === 'provider') return { name: 'vendor-dashboard' }
      return { name: 'dashboard' }
    })
    const dashboardLabel = computed(() => {
      if (role.value === 'parent') return 'Parent Dashboard'
      if (role.value === 'provider') return 'Vendor Dashboard'
      return 'Dashboard'
    })

    const toggleMenu = () => { isOpen.value = !isOpen.value }
    const closeMenu = () => { isOpen.value = false; dropdown.value = false }
    const onScroll = () => { isScrolled.value = window.scrollY > 16 }

    const handleLogout = async () => {
      await auth.logout()
      closeMenu()
      window.location.href = '/'
    }

    const toggleDropdown = () => { dropdown.value = !dropdown.value; if (dropdown.value) notifOpen.value = false }
    const toggleNotif = async () => {
      notifOpen.value = !notifOpen.value
      if (notifOpen.value) {
        dropdown.value = false
        try { await store.fetchConversations() } catch(_) {}
      }
    }
    const onClickOutside = (e) => {
      // Close account dropdown if clicked outside
      if (dropdown.value) {
        const el = dropdownWrap.value
        if (el && !el.contains(e.target)) dropdown.value = false
      }
      // Close notifications dropdown if clicked outside
      if (notifOpen.value) {
        const el2 = notifWrap.value
        if (el2 && !el2.contains(e.target)) notifOpen.value = false
      }
    }

    onMounted(() => {
      // scroll state
      onScroll()
      window.addEventListener('scroll', onScroll, { passive: true })
      // outside click for account dropdown
      document.addEventListener('click', onClickOutside, { passive: true })
      // unread notifications polling
      store.refreshUnread()
      unreadTimer = setInterval(() => store.refreshUnread(), 10000)
    })
    onBeforeUnmount(() => {
      window.removeEventListener('scroll', onScroll)
      document.removeEventListener('click', onClickOutside)
      if (unreadTimer) clearInterval(unreadTimer)
    })

    const cartCount = computed(() => cart.count())
    const recentUnread = computed(() => {
      try {
        return (store.state.conversations || [])
          .filter(c => (c.unread || 0) > 0)
          .slice(0, 5)
      } catch { return [] }
    })
    const conversationTitle = (c) => {
      if (c?.title) return c.title
      const names = (c?.participants || []).map(p => p.name).join(', ')
      return names || 'New message'
    }
    const openConv = async (id) => {
      try {
        await store.openConversation(id)
        notifOpen.value = false
        const c = (store.state.conversations || []).find(x => x.id === id)
        const isSupport = (c?.title || '').toLowerCase().includes('support')
        if (isSupport) {
          await router.push({ name: 'messages', query: { c: id } })
        } else if (userRole.value === 'provider') {
          await router.push({ name: 'vendor-communications' })
        } else {
          // fallback: non-support convo for non-provider goes to messages list
          await router.push({ name: 'messages', query: { c: id } })
        }
      } catch(_) {}
    }
    const openNotificationsCenter = async () => {
      notifOpen.value = false
      if (userRole.value === 'provider') {
        await router.push({ name: 'vendor-communications' })
      } else {
        await router.push({ name: 'messages' })
      }
    }
    const toggleCart = () => { cart.toggleSidebar(); closeMenu() }
    const submitSearch = () => {
      const q = (searchTerm.value || '').trim()
      if (!q) return
      try {
        const raw = localStorage.getItem('search_hits')
        const obj = raw ? JSON.parse(raw) : {}
        obj[q] = (obj[q] || 0) + 1
        localStorage.setItem('search_hits', JSON.stringify(obj))
      } catch(_) {}
      router.push({ name: 'products', query: { q } })
      closeMenu()
    }
    
    return {
      isOpen,
      dropdown,
      dropdownWrap,
      notifOpen,
      notifWrap,
      isScrolled,
      isAuthed,
      userName,
      userRole,
      isAdminUser,
      dashboardRoute,
      dashboardLabel,
      toggleMenu,
      closeMenu,
      handleLogout,
      toggleDropdown,
      toggleNotif,
      cartCount,
      toggleCart,
      searchTerm,
      submitSearch,
      recentUnread,
      conversationTitle,
      openConv,
      openNotificationsCenter,
      store,
    }
  }
}
</script>

<style scoped>
.sp-navbar.scrolled {
  --bg: rgba(255, 255, 255, 0.7);
}
.sp-navbar.scrolled {
  --bg: rgba(255, 255, 255, 0.7);
}

.brand-logo { height: 32px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,.1)); }
.brand { color: #0f172a; }

/* Links area */
.links-wrapper {
  display: flex;
  align-items: center;
}
.nav-list { list-style: none; display: flex; gap: 1rem; margin: 0; padding: 0; }
.nav-link { position: relative; color: #334155; text-decoration: none; padding: .5rem .25rem; font-size: 1.05rem; }
.nav-link::after {
  content: ""; position: absolute; left: 0; bottom: 0; height: 2px; width: 0; background: linear-gradient(90deg, #6366f1, #06b6d4);
  transition: width .25s ease;
}
.nav-link:hover::after, .router-link-active.nav-link::after { width: 100%; }
.admin-link::after {
  background: linear-gradient(90deg, #ffff00, #ffff00);
}

/* Auth area */
.auth-area { display: flex; align-items: center; gap: .75rem; margin-left: 1.25rem; }
.dropdown { position: relative; }
.dropdown-menu {
  position: absolute; min-width: 160px; right: 0; top: calc(100% + 6px);
  background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: .75rem; box-shadow: 0 10px 30px rgba(2,6,23,.12);
  padding: .5rem; z-index: 10;
}
.dropdown-item { width: 100%; background: transparent; border: 0; text-align: left; padding: .5rem .75rem; border-radius: .5rem; color: #0f172a; }
.dropdown-item:hover { background: rgba(99,102,241,.08); }

/* Mobile menu */
.menu-btn { background: transparent; border: 0; width: 40px; height: 32px; position: relative; display: grid; place-items: center; }
.menu-btn span { display: block; width: 24px; height: 2px; background: #0f172a; transition: transform .25s ease, opacity .25s ease; }
.menu-btn span + span { margin-top: 5px; }
.menu-btn span.open:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.menu-btn span.open:nth-child(2) { opacity: 0; }
.menu-btn span.open:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* Mobile behavior */
@media (max-width: 991.98px) {
  .links-wrapper { position: fixed; inset: 64px 0 0 0; background: rgba(255,255,255,.85); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); transform: translateY(-8px); opacity: 0; pointer-events: none; transition: opacity .25s ease, transform .25s ease; display: block; }
  .links-wrapper.open { opacity: 1; transform: translateY(0); pointer-events: auto; }
  .nav-list { flex-direction: column; gap: .25rem; padding: 1rem; }
  .auth-area { padding: 0 1rem 1rem; margin-left: 0; }
}

/* Mount animation */
.fade-slide-enter-from { opacity: 0; transform: translateY(-10px); }
.fade-slide-enter-active { transition: all .35s ease; }
.fade-slide-enter-to { opacity: 1; transform: translateY(0); }

/* Subtle glow line */
.top-glow { position: absolute; inset: 0 0 auto 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(99,102,241,.35), rgba(6,182,212,.35), transparent); opacity: .8; }

/* Notifications dropdown */
.notif-menu { position: absolute; right: 0; top: calc(100% + 6px); width: 280px; background: #fff; border: 1px solid rgba(0,0,0,.06); border-radius: .75rem; box-shadow: 0 10px 30px rgba(2,6,23,.12); overflow: hidden; z-index: 20; }
.notif-header { padding: .5rem .75rem; border-bottom: 1px solid rgba(2,6,23,.06); background: #f8fafc; }
.notif-item { display: block; width: 100%; text-align: left; padding: .5rem .75rem; background: transparent; border: 0; border-bottom: 1px solid rgba(2,6,23,.06); }
.notif-item:hover { background: rgba(99,102,241,.06); }
.xsmall { font-size: .75rem; }
</style>
