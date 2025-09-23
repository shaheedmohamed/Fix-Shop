<template>
  <div>
    <!-- Top navbar only on lg+ and only for non-admin routes -->
    <Navbar v-if="!isAdmin" class="d-none d-lg-block" />
    <router-view v-slot="{ Component }">
      <transition name="route-fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
    <!-- Mobile bottom nav on public (non-admin) routes -->
    <BottomNav v-if="!isAdmin" class="d-lg-none" />
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from './components/layouts/Navbar.vue'
import BottomNav from './components/BottomNav.vue'

export default {
  name: 'AppRoot',
  components: { Navbar, BottomNav },
  setup(){
    const route = useRoute()
    const isAdmin = computed(() => route.matched.some(r => r.meta?.admin))
    return { isAdmin }
  }
}
</script>
