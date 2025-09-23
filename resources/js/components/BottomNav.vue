<template>
  <nav class="sp-bottom-nav d-lg-none">
    <RouterLink class="bn-item" :to="{ name: 'home' }" :class="isActive('home')">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </RouterLink>
    <RouterLink class="bn-item" :to="{ name: 'features' }" :class="isActive('features')">
      <i class="fa-solid fa-table-cells-large"></i>
      <span>Features</span>
    </RouterLink>
    <RouterLink class="bn-item main" :to="{ name: 'chat' }" :class="isActive('chat')">
      <i class="fa-solid fa-wand-magic-sparkles"></i>
      <span>lumix</span>
    </RouterLink>
    <RouterLink class="bn-item" :to="{ name: 'subjects' }" :class="isActive('subjects')">
      <i class="fa-solid fa-book-open"></i>
      <span>Subjects</span>
    </RouterLink>
    <RouterLink class="bn-item" :to="accountRoute" :class="isActive(accountRoute.name)">
      <i class="fa-regular fa-user"></i>
      <span>Account</span>
    </RouterLink>
  </nav>
  
</template>

<script>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import auth from '../store/auth'

export default {
  name: 'BottomNav',
  setup(){
    const route = useRoute()
    const isActive = (name) => ({ active: route.name === name })
    const accountRoute = computed(() => {
      if (!auth.isAuthenticated()) return { name: 'login' }
      const role = auth.state.user?.role
      return role === 'parent' ? { name: 'parent-dashboard' } : { name: 'dashboard' }
    })
    return { isActive, accountRoute }
  }
}
</script>

<style scoped>
.sp-bottom-nav{
  position: fixed; left: 0; right: 0; bottom: 0; z-index: 1030;
  display: grid; grid-template-columns: repeat(5, 1fr);
  background: rgba(255,255,255,.85);
  backdrop-filter: blur(12px) saturate(160%);
  -webkit-backdrop-filter: blur(12px) saturate(160%);
  border-top: 1px solid rgba(0,0,0,.06);
  box-shadow: 0 -10px 30px rgba(2,6,23,.08);
  padding: .25rem max(env(safe-area-inset-left), 12px) calc(.25rem + env(safe-area-inset-bottom)) max(env(safe-area-inset-right), 12px);
}
.bn-item{ display: flex; flex-direction: column; align-items: center; justify-content: center; padding: .35rem 0; color: #334155; text-decoration: none; gap: 2px; }
.bn-item i{ font-size: 1.15rem; }
.bn-item span{ font-size: .7rem; }
.bn-item.active{ color: #0d6efd; }
.bn-item.main{
  position: relative;
}
.bn-item.main i{
  font-size: 1.35rem;
}
.bn-item.main::before{
  content: "";
  position: absolute; inset: auto 30% -8px 30%; height: 36px; border-radius: 999px; z-index: -1;
  background: radial-gradient(closest-side, rgba(13,110,253,.15), transparent 70%);
}
</style>
