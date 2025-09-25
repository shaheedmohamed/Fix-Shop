<template>
  <div class="secondary-nav">
    <div class="container d-flex align-items-center gap-3 overflow-auto no-scrollbar py-2">
      <button class="btn btn-link text-decoration-none text-light d-flex align-items-center" @click="toggleAll">
        <i class="fa-solid fa-bars me-2"></i> All
      </button>
      <RouterLink to="/deals" class="sec-link">Today's Deals</RouterLink>
      <RouterLink to="/category/mobiles" class="sec-link">Mobile Phones</RouterLink>
      <RouterLink to="/category/electronics" class="sec-link">Electronics</RouterLink>
      <RouterLink to="/category/fashion" class="sec-link">Fashion</RouterLink>
      <RouterLink to="/" class="sec-link">Home</RouterLink>
      <RouterLink to="/category/games" class="sec-link">Video Games</RouterLink>
      <RouterLink to="/category/appliances" class="sec-link">Appliances</RouterLink>
      <RouterLink to="/category/grocery" class="sec-link">Grocery</RouterLink>
      <RouterLink to="/category/perfumes" class="sec-link">Perfumes</RouterLink>
      <RouterLink to="/account" class="sec-link">Your account</RouterLink>
    </div>

    <!-- Slideout categories panel -->
    <transition name="fade">
      <aside v-if="showAll" class="all-panel">
        <div class="panel-header d-flex align-items-center justify-content-between">
          <strong>Categories</strong>
          <button class="btn btn-sm btn-light" @click="toggleAll"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="list-group list-group-flush small">
          <RouterLink v-for="(c,i) in categories" :key="i" :to="c.to" class="list-group-item list-group-item-action">
            {{ c.title }}
          </RouterLink>
        </div>
      </aside>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'SecondaryNav',
  data(){
    return {
      showAll: false,
      categories: [
        { title: 'New in', to: '/new' },
        { title: 'Automobile', to: '/category/auto' },
        { title: 'Beauty & Health', to: '/category/beauty' },
        { title: 'Baby & Maternity', to: '/category/baby' },
        { title: 'Jewelry & Accessories', to: '/category/jewelry' },
        { title: 'Industry & Science', to: '/category/industry' },
        { title: 'Electronics', to: '/category/electronics' },
        { title: 'Home & Kitchen', to: '/category/home' },
        { title: 'Office & School', to: '/category/office' },
        { title: 'Men Large Sizes', to: '/category/plus-men' },
      ]
    }
  },
  methods: {
    toggleAll(){ this.showAll = !this.showAll }
  }
}
</script>

<style scoped>
.secondary-nav { background: #0f2133; color: #cfe0f2; }
.sec-link { color: #cfe0f2; text-decoration: none; white-space: nowrap; }
.sec-link:hover { color: #fff; text-decoration: underline; }
.no-scrollbar { scrollbar-width: none; }
.no-scrollbar::-webkit-scrollbar { display: none; }

.all-panel {
  position: fixed;
  top: 0; left: 0; bottom: 0;
  width: 320px;
  background: #fff;
  box-shadow: 0 0 20px rgba(0,0,0,.2);
  z-index: 1055;
  padding: 12px;
  overflow: auto;
}
.panel-header { border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px; }

.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
