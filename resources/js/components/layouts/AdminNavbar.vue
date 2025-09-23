<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
      <button class="btn btn-outline-light d-lg-none me-2" @click="$emit('toggleSidebar')">
        <i class="fa-solid fa-bars"></i>
      </button>
      <span class="navbar-brand fw-bold">
        <i class="fa-solid fa-shield-halved me-2"></i>Admin
      </span>
      <div class="ms-auto d-flex align-items-center gap-2">
        <span class="text-white-50 small d-none d-md-inline">{{ userName }} ({{ role }})</span>
        <button class="btn btn-sm btn-outline-light" @click="logout"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button>
      </div>
    </div>
  </nav>
</template>

<script>
import auth from '../../store/auth'
export default {
  name: 'AdminNavbar',
  emits: ['toggleSidebar'],
  computed:{
    userName(){ return auth.state.user?.name || 'Admin' },
    role(){ return auth.state.user?.role || '-' }
  },
  methods:{
    async logout(){
      await auth.logout()
      this.$router.push({ name:'home' })
    }
  }
}
</script>
