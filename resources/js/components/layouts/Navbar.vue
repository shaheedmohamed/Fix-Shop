<template>
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
            <li><RouterLink class="nav-link" :to="{ name: 'features' }" @click="closeMenu">Features</RouterLink></li>
            <li><RouterLink class="nav-link" :to="{ name: 'about' }" @click="closeMenu">About</RouterLink></li>
            <li><RouterLink class="nav-link" :to="{ name: 'contact' }" @click="closeMenu">Contact</RouterLink></li>
            <li><RouterLink class="nav-link" :to="{ name: 'products' }" @click="closeMenu">Products</RouterLink></li>
            <li v-if="isAdminUser">
              <RouterLink class="nav-link admin-link" :to="{ name: 'admin-dashboard' }" @click="closeMenu">
                <i class="fa-solid fa-user-shield me-2"></i>
                Admin
              </RouterLink>
            </li>
          </ul>

          <div class="auth-area">
            <template v-if="!isAuthed">
              <RouterLink class="btn btn-outline-primary me-2" :to="{ name: 'login' }" @click="closeMenu">Login</RouterLink>
              <RouterLink class="btn btn-primary" :to="{ name: 'register' }" @click="closeMenu">Register</RouterLink>
            </template>
            <template v-else>
              <RouterLink class="btn btn-outline-primary me-2" :to="dashboardRoute" @click="closeMenu">
                <i class="fa-solid fa-gauge-high me-2"></i>{{ dashboardLabel }}
              </RouterLink>
              <RouterLink class="btn btn-light position-relative me-2" :to="{ name: 'cart' }" @click="closeMenu" title="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span v-if="cartCount>0" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">{{ cartCount }}</span>
              </RouterLink>
              <div class="dropdown" ref="dropdownWrap">
                <button class="btn btn-light d-flex align-items-center" @click="toggleDropdown" :aria-expanded="dropdown ? 'true' : 'false'" aria-haspopup="menu">
                  <i class="fa-regular fa-user me-2"></i>{{ userName }}
                  <i class="fa-solid fa-chevron-down ms-2 small"></i>
                </button>
                <div class="dropdown-menu end" v-if="dropdown" role="menu">
                  <RouterLink class="dropdown-item" :to="{ name: 'profile' }" @click="closeMenu"><i class="fa-regular fa-id-badge me-2"></i>Profile</RouterLink>
                  <RouterLink class="dropdown-item" :to="{ name: 'advisor' }" @click="closeMenu"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Create plan with lumix</RouterLink>
                  <button class="dropdown-item" @click="handleLogout"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
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
</template>

<script>
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
import auth from '../../store/auth'
import store from '../../store/messages'
import cart from '../../store/cart'

export default {
  name: 'NavbarLayout',
  setup(_, { emit, expose }){
    const isOpen = ref(false)
    const dropdown = ref(false)
    const dropdownWrap = ref(null)
    const isScrolled = ref(false)

    const isAuthed = computed(() => auth.isAuthenticated())
    const userName = computed(() => auth.state.user?.name || 'Account')
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

    const toggleDropdown = () => { dropdown.value = !dropdown.value }
    const onClickOutside = (e) => {
      if (!dropdown.value) return
      const el = dropdownWrap.value
      if (el && !el.contains(e.target)) dropdown.value = false
    }

    let unreadTimer = null
    onMounted(() => {
      onScroll()
      window.addEventListener('scroll', onScroll, { passive: true })
      document.addEventListener('click', onClickOutside, { passive: true })
      // messages unread polling
      store.refreshUnread()
      unreadTimer = setInterval(() => store.refreshUnread(), 10000)
    })
    onBeforeUnmount(() => {
      window.removeEventListener('scroll', onScroll)
      document.removeEventListener('click', onClickOutside)
      if (unreadTimer) clearInterval(unreadTimer)
    })

    const cartCount = computed(() => cart.count())
    return { isOpen, dropdown, isScrolled, isAuthed, userName, isAdminUser, dashboardRoute, dashboardLabel, toggleMenu, closeMenu, handleLogout, toggleDropdown, dropdownWrap, store, cartCount }
  }
}
</script>

<style scoped>
/* Container and glass effect */
.sp-navbar {
  --bg: rgba(255, 255, 255, 0.3);
  --bd: rgba(255, 255, 255, 0.35);
  --shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
  position: sticky;
  top: 0;
  z-index: 1030;
  background: var(--bg);
  backdrop-filter: saturate(160%) blur(16px);
  -webkit-backdrop-filter: saturate(160%) blur(16px);
  border-bottom: 1px solid var(--bd);
  box-shadow: var(--shadow);
  padding: 14px 0; /* increase height */
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
</style>
