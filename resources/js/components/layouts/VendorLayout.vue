<template>
  <div class="admin-wrap">
    <aside class="admin-aside">
      <div class="brand d-flex align-items-center mb-3">
        <img src="/images/2-removebg-preview.png" alt="Fix & Shop" class="brand-logo me-2" />
        <div class="fw-bold">Vendor Panel</div>
      </div>
      <div class="menu">
        <RouterLink :to="{ name: 'vendor-dashboard' }" class="menu-item" :class="{ active: routeName==='vendor-dashboard' }">
          <i class="fa-solid fa-gauge-high me-2"></i>
          <span>Dashboard</span>
        </RouterLink>
        <div class="menu-section">Manage</div>
        <RouterLink :to="{ name: 'vendor-products' }" class="menu-item" :class="{ active: routeName==='vendor-products' }">
          <i class="fa-solid fa-box me-2"></i>
          <span>Products</span>
        </RouterLink>
        <RouterLink :to="{ name: 'vendor-posts' }" class="menu-item" :class="{ active: routeName==='vendor-posts' }">
          <i class="fa-regular fa-newspaper me-2"></i>
          <span>Posts</span>
        </RouterLink>
        <div class="menu-section text-muted">Communications (soon)</div>
      </div>
    </aside>

    <main class="admin-main">
      <header class="admin-header">
        <div>
          <div class="welcome">Welcome, {{ userName }} (Provider)</div>
        </div>
        <div class="header-actions">
          <RouterLink class="btn btn-sm btn-outline-secondary" :to="{ name: 'products' }">
            <i class="fa-solid fa-store me-2"></i>Public Products
          </RouterLink>
        </div>
      </header>
      <section class="admin-content">
        <router-view />
      </section>
    </main>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import auth from '../../store/auth'
export default {
  name: 'VendorLayout',
  setup(){
    const route = useRoute()
    const routeName = computed(() => route.name)
    const userName = computed(() => auth.state.user?.name || 'Vendor')
    return { routeName, userName }
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
.menu-item.active { background: #c7f9cc; color: #111827; }
.admin-main { padding: 16px 20px; }
.admin-header { background: #fff; border: 1px solid rgba(2,6,23,.06); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; }
.admin-content { margin-top: 14px; }
</style>
